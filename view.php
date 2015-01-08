<html>
    <head>
        <title>Grid</title>
        <link href="dist/css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="dist/css/bootstrap-theme.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="container col-md-10">

            <br/>

            <?php
            require 'Agridview.php';

            Agridview::pagination('view.php', 'employee', array(
                'id' => 'emp-grid',
                'urlvariable' => 'page',
                'column' => array(
                    'emp_id',
                    'emp_name',
                    'emp_salary',
                ),
                'search' => true,
                'condition' => array(
                //'emp_salary' => '54646'
                ),
                'sort' => array(
                    'emp_id' => 'ASC'
                ),
                'limit' => 8,
                'AbuttonColumn' => array(
                    'view' => array(
                        'label' => 'View',
                        'url' => 'v.php',
                        'class' => 'btn btn-success'
                    ),
                    'update' => array(
                        'label' => 'Update',
                        'url' => 'u.php',
                        'class' => 'btn btn-info'
                    ),
                    'delete' => array(
                        'label' => 'Delete',
                        'url' => 'd.php',
                        'class' => 'btn btn-danger'
                    ),
                )
            ));
            ?>
        </div>
    </body>
</html>


