<?php

namespace IntervalsFinder;

require_once __DIR__ . DS . 'TimeSlot.php';

class IntervalsFinder
{
    /**
     * IntervalsFinder constructor.
     * @param \DBconn $sql
     */
    public function __construct(\DBconn $sql)
    {
        $this->sql = $sql;
    }

    /**
     * @param int $doctorId
     * @param string $date
     * @param int $timeInterval
     * @return array
     * @throws \Exception
     */
    public function find($doctorId, $date, $timeInterval)
    {
        $schedules = $this->getSchedules($doctorId, $date);

        $obstacles = $this->getObstacles($doctorId, $date);

        $result = array_map(function ($schedule) use ($obstacles, $timeInterval) {
            $timeStart = $schedule->time_start;
            $timeEnd = $schedule->time_end;

            $relevant = $this->getRelevantDates($obstacles, $timeStart, $timeEnd);
            $visits = $this->padVisits($relevant, $timeStart, $timeEnd);
            return $this->findIntervals($visits, $timeInterval);
        }, $schedules);

        if ($result === []) {
            return [];
        }

        // Объединяет подмассивы в единый массив.
        $merged = call_user_func_array('array_merge', $result);

        $unique = array_unique($merged, SORT_REGULAR);

        // Для переприсваивания ключей.
        return array_values($unique);
    }

    /**
     * @param TimeSlot[] $data
     * @param int $timeInterval
     * @return array
     * @throws \Exception
     */
    private function findIntervals(array $data, $timeInterval)
    {
        $result = [];

        foreach ($data as $i => $date) {
            $previous = $i - 1;

            /**
             * @var \DateTime $start
             */
            $start = isset($data[$previous]) ? $data[$previous]->getEnd(): $date->getEnd();

            /**
             * @var \DateTime $end
             */
            $end = $date->getStart();

            $hasTime = $this->hasTime($end->diff($start), $timeInterval);
            if (!$hasTime) {
                continue;
            }

            if ($i === 0) {
                $result[] = new TimeSlot(
                    $date->getStart()->format('h:i'),
                    $date->getStart()->format('h:i')
                );
                continue;
            }

            while ($start < $end) {
                $from = $start->format('H:i');
                // Метод add изменяет переменную $start.
                $to = $start->add(new \DateInterval("PT{$timeInterval}M"));

                if ($to > $end) {
                    continue;
                }

                $result[] = new TimeSlot($from, $to->format('H:i'));
            }
        }

        return $result;
    }

    /**
     * @param \DateInterval $diff
     * @param int $max
     * @return bool
     */
    private function hasTime(\DateInterval $diff, $max)
    {
        return $diff->h * 60 + $diff->i >= $max;
    }

    /**
     * @param array $visits
     * @param string $start
     * @param string $end
     * @return array
     */
    private function padVisits(array $visits, $start, $end)
    {
        array_unshift(
            $visits, new TimeSlot($start, $start)
        );

        $visits[] = new TimeSlot($end, $end);

        return $visits;
    }

    /**
     * @param int $doctorId
     * @param string $date
     * @return array
     */
    private function getSchedules($doctorId, $date)
    {
        $this->sql->query('
            SELECT 
                time_start, time_end
            FROM 
                ' . DB_TABLE_NEW_SCHEDULE_DOCTOR . ' 
            WHERE 
                id_doctor = ' . $doctorId . ' AND `date` = "' . $date . '" 
            ORDER BY 
                time_start
        ');

        if ($this->sql->getNumberRows() < 1) {
            return [];
        }

        return $this->sql->getFetchObject();
    }

    /**
     * @param int $doctorId
     * @param string $date
     * @return array|\stdClass[]
     */
    private function getVisits($doctorId, $date)
    {
        // Добавляет 5 минут для поправки на особенность системы.
        $this->sql->query('
        SELECT
            time_start, DATE_ADD(time_end, INTERVAL 5 MINUTE) AS time_end
        FROM 
            ' . DB_TABLE_PATIENT_FILE . '
        WHERE
            id_doctor = ' . $doctorId . ' AND `date` = "' . $date . '"
        GROUP BY 
            time_start, time_end
        ORDER BY 
            time_start
    ');

        if ($this->sql->getNumberRows() < 1) {
            return [];
        }

        return $this->extractTime($this->sql->getFetchObject());
    }

    /**
     * @param int $doctorId
     * @param string $date
     * @return array|\stdClass[]
     */
    private function getFailures($doctorId, $date)
    {
        // Добавляет 5 минут для поправки на особенность системы.
        $this->sql->query('
            SELECT
                time_failure_start AS time_start, DATE_ADD(time_failure_end, INTERVAL 5 MINUTE) AS time_end
            FROM 
                ' . DB_TABLE_MORE_FAILURE_DOCTOR . '
            WHERE
                id_people = ' . $doctorId . ' AND date_failure = "' . $date . '"
            GROUP BY 
                time_failure_start, time_failure_end
            ORDER BY 
                time_failure_start
        ');

        if ($this->sql->getNumberRows() < 1) {
            return [];
        }

        return $this->extractTime($this->sql->getFetchObject());
    }

    /**
     * Убирает из массива $dates все записи меньше $from или больше $to.
     * @param array $dates
     * @param string $from
     * @param string $to
     * @return array
     */
    private function getRelevantDates(array $dates, $from, $to)
    {
        $dateTimeFrom = new \DateTime($from);
        $dateTimeTo = new \DateTime($to);
        return array_filter($dates, function ($date) use ($dateTimeFrom, $dateTimeTo) {
            return $date->start >= $dateTimeFrom && $date->end <= $dateTimeTo;
        });
    }

    /**
     * сотрировка перерывов в расписании
     *
     * @param int $doctorId
     * @param string $date
     * @return array|\stdClass[]
     */
    private function getObstacles($doctorId, $date)
    {
        $obstacles = array_merge(
            $this->getVisits($doctorId, $date),
            $this->getFailures($doctorId, $date)
        );

        usort($obstacles, function ($left, $right) {
            if ($left->start < $right->start) {
                return -1;
            }

            if ($left->start > $right->start) {
                return 1;
            }

            return 0;
        });

        return $obstacles;
    }

    /**
     * Принимает массив объектов с атрибутами time_start и time_end и формирует из них массив объектов TimeSlot.
     * @param \stdClass[] $data
     * @return \stdClass[] Массив объектов TimeSlot.
     */
    private function extractTime(array $data)
    {
        $result = [];

        foreach ($data as $row) {
            $result[] = new TimeSlot($row->time_start, $row->time_end);
        }

        return $result;
    }

    /**
     * @var \DBconn
     */
    private $sql;
}