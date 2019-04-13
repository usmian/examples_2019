<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagCommonRepository")
 * @ORM\Table(name="tag_common", indexes={@ORM\Index(name="searchs_idx", columns={"name"})})
 */
class TagCommon
{
    const TYPE_GOODS = 1;
    const TYPE_REGIONS = 2;
    const TYPE_DELIVERY = 3;
    const TYPE_REQUIREMENTS = 4;
    const TYPE_CUSTOM = 5;

    public static $mapTypes = [
        self::TYPE_GOODS => 'Товарные группы',
        self::TYPE_REGIONS => 'Маршруты',
        self::TYPE_DELIVERY => 'Условия поставок',
        self::TYPE_REQUIREMENTS => 'Потребности клиента',
        self::TYPE_CUSTOM => 'Другое',

    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $is_archive;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_name;
    /**
     *
     *
     * @ORM\ManyToMany(targetEntity="Contragent", mappedBy="tagsCommon")
     */
    protected $contragents;

    public function __construct()
    {
        $this->contragents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsArchive(): ?int
    {
        return $this->is_archive;
    }

    public function setIsArchive(int $is_archive): self
    {
        $this->is_archive = $is_archive;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(string $type_name): self
    {
        $this->type_name = $type_name;

        return $this;
    }
}
