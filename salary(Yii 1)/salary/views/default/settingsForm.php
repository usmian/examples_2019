<?
/**
 * @var Service $services
 * @var UserSalary $model
 * @var BsActiveForm $form
 */

$title = 'Настройка зарплаты сотрудника ';
$this->pageTitle = $title . '- Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array(
    'Зарплаты' => array('/salary'),
    'Настройка' => '/salary/default/settings',
    $title
));

?>

<?
$this->submenu(array(
    array(
        'link' => '/salary/default/settings',
        'title' => 'Настройка ЗП', 'icon' => 'ti-angle-left'
    ),
    array(
        'link' => '/salary/default/settingsUpdate/id/' . $model->user_id,
        'title' => $title, 'icon' => ''
    ),

));
?>


<?php $form = $this->beginWidget('bootstrap.widgets.BsActiveForm', array(
    'id' => 'createUpdateSalary',
    'enableAjaxValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => false,
        'validateOnChange' => false,
    ),
    'htmlOptions' => array('class' => ''),
)); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Заработная плата
            </div>
            <div class="panel-body">

                <div class="user-info-wrap">
                    <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                 <label>Сотрудник:</label>
                                 <span class="salary-title "><?= $model->getUserName(); ?></span>
                             </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Должностные роли:</label>
                                <span class="salary-title "><?= $model->getUserRoles(); ?></span>
                            </div>
                        </div>
                        <? if (User::isDoctor($model->user_id)) : ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Специальности:</label>
                                <span class="salary-title"><?= $model->getUserProfessions(); ?></span>
                            </div>
                        </div>
                        <? endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <?php echo $form->textFieldControlGroup($model, 'value'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <? if (!$model->isNewRecord) : ?>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Комиссия за услуги</div>
                <div class="panel-body">

                    <?
                    $this->widget('bootstrap.widgets.BsGridView', array(
                            'type' => array(BSHtml::GRID_TYPE_STRIPED, BSHtml::GRID_TYPE_HOVER),
                            'id' => 'grid-salary-settings',
                            'dataProvider' => $services->search(false, null, $model->user_id),
                            'filter' => $services,
                            'ajaxUpdate' => true,
                            'filterSelector' => '.my-filter',
                            'afterAjaxUpdate' => 'js:function() {
                                    salaryServices.render();
                                 }',
                            'template' => "{summary}{pager}\n{items}\n{pager}",
                            'columns' => array(
                                'num' => array('name' => 'num', 'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)', 'htmlOptions' => array('width' => '3%'), 'filter' => false),

                                array('name' => 'code', 'type' => 'raw', 'value' => '$data->code', 'filter' => BSHtml::activeTextField($services, 'code', ['class' => 'my-filter']), 'sortable' => true, 'htmlOptions' => array('width' => '7%')),
                                array('name' => 'name', 'type' => 'raw', 'value' => '$data->name', 'filter' => BSHtml::activeTextField($services, 'name', ['class' => 'my-filter']), 'htmlOptions' => array('width' => '20%')),
                                array('name' => 'service_category_id', 'type' => 'raw', 'value' => '$data->category->title', 'filter' => BSHtml::activeDropDownList($services, 'service_category_id', ServiceCategory::model()->getList(), ['empty' => '', 'class' => 'selectbox form-control my-filter']),
                                    'htmlOptions' => array('width' => '15%')),
                                array('name' => 'profession_id', 'type' => 'raw', 'value' => '$data->getProfession($data->profession_id)', 'filter' => BSHtml::activeDropDownList($services, 'profession_id', Profession::model()->getList(), ['empty' => '', 'class' => 'selectbox form-control my-filter']),
                                    'htmlOptions' => array('width' => '15%')),
                                array('name' => 'price', 'type' => 'raw', 'value' => 'Helper::price($data->price)', 'filter' => false, 'sortable' => true, 'htmlOptions' => array('width' => '14%')),
                                array('name' => '_salary_service_value', 'type' => 'raw', 'value' => '$data->getValueHtml()', 'filter' => false, 'sortable' => true, 'htmlOptions' => array('width' => '12%'), 'visible' => (User::isDoctor($model->user_id)) ? true : false),
                                array('name' => '_recommend_value', 'type' => 'raw', 'value' => '$data->getValueRecommendationHtml()', 'filter' => false, 'sortable' => true, 'htmlOptions' => array('width' => '12%')),
                            ),
                            'enableSorting' => true,
                            'emptyText' => 'Услуги для данной специальности не найдены',
                            'ajaxUrl' => Yii::app()->createUrl('salary/default/settingsUpdate/id/' . $id)
                        )
                    );
                    ?>
                </div>
            </div>
        </div>

    <? endif; ?>

    <div class="col-md-12">
        <div class="panel panel-buttons">
            <div class="panel-body">
                <div class="submit">
                    <?= CHtml::submitButton($model->isNewRecord ? 'Продолжить' : 'Сохранить', array('class' => 'btn btn-success')) ?>
                    <a class="btn btn-default" href="<?= Yii::app()->createUrl('salary/default/settings') ?>">Отмена</a>
                </div>
            </div>
        </div>
    </div>

</div>


<? $this->scripts($this->module->assetsUrl . '/js/salaryServices.js', true); ?>
<? $this->scripts($this->module->assetsUrl . '/js/salary.js', true) ?>

<input type="hidden" class="is-doctor" value="<?=User::isDoctor($model->user_id)?>"/>
<input type="hidden" id="user-id" value="<?= $id ?>"/>
<?php $this->endWidget(); ?>

<script type="text/template" id="tpl-discount">
    <div class="row">
        <div class="col-md-9">
            <div class="discount-wrap-<%=wrap_class%> discount-wrap inline pull-left"><input type="text" class="discount spinner"/></div>
            <div class="btn btn-sm btn-default all-<%=btn_class%>-save"><span class="ti-check"></span></div>
        </div>
        <div class="col-md-3"></div>
    </div>
</script>