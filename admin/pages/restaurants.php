<?php
require_once "adminModule.class.php";

class Restaurants extends AdminModule {

    protected static $_title = "Ресторан";
    protected static $_DB_table = 'rest';

    public static function initModule () {
        self::setRestId($_SESSION['admin']['restaurant_id']);
        self::start();
    }

    // Добавление ресторана
    public static function add() {

        $form = Form::newForm('restaurants','restForm',DBP::getPrefix().self::getDbTable());

        $form->addfield(array('name' => 'rest_title',
                'caption' => 'Название',
                'is_required' => true,
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );
        $form->addfield(array('name' => 'rest_uri',
                'caption' => 'Ссылка(uri)',
                'is_unique' => true,
                'is_required' => true,
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );
        $form->addfield(array('name' => 'rest_address',
                'caption' => 'Адрес',
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_ostanovka',
                'caption' => 'Остановка',
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_metro',
                'caption' => 'Ближайшая станция метро',
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_site',
                'caption' => 'Сайт',
                'pattern' => 'text',
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_phone',
                'caption' => 'Телефон',
                'pattern' => 'phone',
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_photo',
                'caption' => 'Основная фотография',
                'pattern' => 'file',
                'formats' => array('jpg','png','jpeg','gif'),
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_logo',
                'caption' => 'Логотип',
                'pattern' => 'file',
                'formats' => array('jpg','png','jpeg','gif'),
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_location_id',
                'caption' => 'Район/метро',
                'pattern' => 'select',
                'css_class' => 'caption',
                'multiple' => false,
                'size' => '1',
                'options' => array_merge(array( 0 => "Укажите район/метро"),
                Form::array_combine(DB::fetchAll('SELECT id,title FROM `kazan_list_location`')))
                )
        );

        $form->addfield(array('name' => 'rest_location_id',
                'caption' => 'Район/метро',
                'pattern' => 'select',
                'css_class' => 'caption',
                'multiple' => false,
                'size' => '1',
                'options' => array_merge(array( 0 => "Укажите средний чек"),
                Form::array_combine(DB::fetchAll('SELECT id,title FROM `list_check`')))
                )
        );

        $form->addfield(array('name' => 'rest_description',
                'caption' => 'Описание',
                'pattern' => 'editor',
                )
        );

        $form->addfield(array('name' => 'rest_google_code',
                'caption' => 'Страница google карты',
                'pattern' => 'textarea',
                'css_class' => ''
                )
        );

        $form->addfield(array('name' => 'submit',

                'caption' => 'Добавить',
                'css_class' => 'ui_button',
                'pattern' => 'submit')
        );

        self::validate($form);
    }

    // Изменение информации о ресторане
    public static function edit() {
        $id = $_SESSION['admin']['restaurant_id'];
        if (!empty($_POST)) {
            $record = $_POST;
        } else {
            $record = DBP::getRecord('rest',"id =".$id);
        }
        $form = Form::newForm('restaurants','restForm',DBP::getPrefix().self::getDbTable());

        $form->addfield(array('name' => 'rest_title',
                'caption' => 'Название',
                'is_required' => true,
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_title'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_uri',
                'caption' => 'Ссылка(uri)',
                'is_unique' => true,
                'is_required' => true,
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_uri'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_address',
                'caption' => 'Адрес',
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_address'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_ostanovka',
                'caption' => 'Остановка',
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_ostanovka'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_metro',
                'caption' => 'Ближайшая станция метро',
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_metro'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_site',
                'caption' => 'Сайт',
                'pattern' => 'text',
                'maxlength' => '255',
                'value' => $record['rest_site'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_phone',
                'caption' => 'Телефон',
                'pattern' => 'phone',
                'maxlength' => '255',
                'value' => $record['rest_phone'],
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_photo',
                'caption' => 'Основная фотография',
                'pattern' => 'file',
                'formats' => array('jpg','png','jpeg','gif'),
                'maxlength' => '255',
                'css_class' => 'caption')
        );
        $form->addfield(array('name' => 'rest_logo',
                'caption' => 'Логотип',
                'pattern' => 'file',
                'formats' => array('jpg','png','jpeg','gif'),
                'maxlength' => '255',
                'css_class' => 'caption')
        );

        $form->addfield(array('name' => 'rest_location_id',
                'caption' => 'Район/метро',
                'pattern' => 'select',
                'css_class' => 'caption',
                'multiple' => false,
                'size' => '1',
                'selected' =>$record['rest_location_id'],
                'options' => array_merge(array( 0 => "Укажите район/метро"),
                Form::array_combine(DB::fetchAll('SELECT id,title FROM `kazan_list_location`'))
                )
        ));

        $form->addfield(array('name' => 'rest_check_id',
                'caption' => 'Средний чек',
                'pattern' => 'select',
                'css_class' => 'caption',
                'multiple' => false,
                'selected' =>$record['rest_check_id'],
                'size' => '1',
                'options' => array_merge(array( 0 => "Укажите средний чек"),
                Form::array_combine(DB::fetchAll('SELECT id,title FROM `list_check`')))
                )
        );

        $form->addfield(array('name' => 'rest_description',
                'caption' => 'Описание',
                'pattern' => 'editor',
                'value' => $record['rest_description'],
                )
        );

        $form->addfield(array('name' => 'rest_google_code',
                'caption' => 'Страница google карты',
                'pattern' => 'textarea',
                'css_class' => '',
                'value' => $record['rest_google_code']
                )
        );

        $form->addfield(array('name' => 'edit',

                'caption' => 'Сохранить',
                'css_class' => 'ui_button',
                'pattern' => 'submit')
        );

        self::validate($form,$id,true);
    }

    public static function goEdit() {
        if (ELEMENT_ID) {
            $id = ELEMENT_ID;
            $_SESSION['admin']['restaurant_id'] = $id;
            $_SESSION['admin']['restaurant'] = DBP::getRecord('rest','id='.DB::quote($id));
            header('Location: admin.php?page=restaurants&action=edit', true, 303);
            die();
        }
    }

    public static function save() {
        $data = array();
        unset($_POST['submit']);
        unset($_POST['confirm']);
        $data = $_POST;
        $data['rest_uri'] = str_replace('-', '_', $data['rest_uri']);
        $data['rest_uri'] = str_replace(' ', '_', $data['rest_uri']);
        if (!empty($_FILES['rest_photo']['name'])) {
            $file = File::saveFile('rest_photo', $_POST['rest_uri'],  Config::getValue('path','upload').'image/restaurant/');
            if (!empty($file)) {
                $data['rest_photo'] = 1;
            } else {
                echo "Ошибка в загрузке фото.";
                return false;
            }
        } else {
            $data['rest_photo']=0;
        }
        if (!empty($_FILES['rest_logo']['name'])) {
            $file = File::saveFile('rest_logo', $_POST['rest_uri'],  Config::getValue('path','upload').'image/rest_logo/');
            if (!empty($file)) {
                $data['rest_logo'] = 1;
            } else {
                echo "Ошибка в загрузке фото.";
                return false;
            }
        } else {
            $data['rest_logo']=0;
        }
        if (trim($data['rest_description'])=='<br />') {
            $data['rest_description']='';
        }
        DBP::insert('rest',$data);
    }

    public static function saveEdit() {
        $data = array();
        unset($_POST['edit']);
        unset($_POST['confirm']);
        $data = $_POST;
        if (!empty($_FILES['rest_photo']['name'])) {
            $file = File::saveFile('rest_photo', $_POST['rest_uri'],  Config::getValue('path','upload').'image/restaurant/');
            if (!empty($file)) {
                $data['rest_photo'] = 1;
            } else {
                echo "Ошибка в загрузке фото.";
                return false;
            }
        } else {
            unset($data['rest_photo']);
        }
        if (!empty($_FILES['rest_logo']['name'])) {
            $file = File::saveFile('rest_logo', $_POST['rest_uri'],  Config::getValue('path','upload').'image/rest_logo/');
            if (!empty($file)) {
                $data['rest_logo'] = 1;
            } else {
                echo "Ошибка в загрузке фото.";
                return false;
            }
        } else {
            unset($data['rest_logo']);
        }
        if (trim($data['rest_description'])=='<br />') {
            $data['rest_description']='';
        }
        DBP::update('rest',$data,'id ='.self::getRestId());

    }

    public static function showList() {
        $list = Form::showJqGrid(
                array(
                'url'=>'/admin/admin.php?page=restaurants&action=showJSON',
                'table'=>'gridlist','pager'=>'gridpager','width'=>'800','height'=>'240'
                ),
                array(
                array('title'=>'id','width'=>'50','align'=>'center'),
                array('title'=>'Название','width'=>'250'),
                array('title'=>'Адрес','width'=>'250'),
                array('title'=>'Действия','align'=>'center','width'=>'250'),
                array('title'=>'Показ на сайте','align'=>'center')
                )
        );
        self::showTemplate($list);
    }

    public static function showJSON() {
        $page = $_POST['page'];
        $limit = $_POST['rows'];
        //Получаем количество ресторанов
        $records = DB::fetch("SELECT COUNT(*) FROM ".DBP::getPrefix()."rest");
        //Вычисляем кол-во страниц
        $count = $records['COUNT(*)'];
        $total_pages = ($count >0) ? ceil($count/$limit) : 0;
        if ($page > $total_pages) $page=$total_pages;
        $start = $limit*$page - $limit;
        $records = DB::fetchAll(
                "SELECT id,rest_title,rest_address,is_hidden FROM ".DBP::getPrefix()."rest ".
                'LIMIT '.$start.' , '.$limit
        );
        foreach ($records as &$record) {
            $editLink = self::getLink(PAGE, 'goEdit', $record['id']);
            $delLink = self::getLink(PAGE,'delete', $record['id']);
            $record['control']="<a href='$editLink'>Редактировать</a> | <a href='$delLink'>Удалить</a>";
            if ($record['is_hidden']) $checked = '';
            else $checked = 'checked';
            unset($record['is_hidden']);
            $record['status']="<input type='checkbox' ".$checked."
                onchange='toggleItem(".$record['id'].")' class='hide_item' />";
        }
        echo Form::arrayToJqGrid($records, $total_pages, $page, $count);
    }
    
    public static function delete() {
        $id = ELEMENT_ID;
        DBP::delete(self::getDbTable(),'id ='.$id);
        header('Location: admin.php?page=restaurants', true, 303);
    }

    /**
     * Скрывает или показывает ресторан на сайте
     * @param int $id id ресторана
     */
    public static function toggleItem($id = null) {
        if (empty($id)) {
            if (!empty($_POST['id'])) {
                $id = intval($_POST['id']);
            } else {
                $id = self::getRestId();
            }
        } else {
            $id = intval($id);
        }
        $status = DBP::getValue('rest', 'is_hidden');
        if ($status) {
            DBP::update('rest', array('is_hidden'=>0), 'id='.$id);
        } else {
            DBP::update('rest', array('is_hidden'=>1), 'id='.$id);
        }
        echo "ok";
    }

}