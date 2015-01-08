<?php

class Agridview {

    static public $dbhost = 'localhost:3307';
    static public $dbuser = 'root';
    static public $dbpass = '';
    static public $dbname = 'test';

    static public function pagination($view = "", $table = "", $options = array()) {

        $dbh = new PDO('mysql:host=' . self::$dbhost . ';dbname=' . self::$dbname, self::$dbuser, self::$dbpass);

        $count = 0;
        $limit = $options['limit'];

        if (isset($_GET[$options['urlvariable']])) {
            if ($_GET[$options['urlvariable']] > 1) {
                $count = $limit * $_GET[$options['urlvariable']];
                $count = $count - $limit;
            }
        }

        //parse columns
        $columns = '';

        foreach ($options['column'] as $column) {
            $columns.=$column . ',';
        }

        $columns = substr($columns, 0, (strlen($columns) - 1));

        //parse conditions
        $conditions = '';

        if (!empty($options['condition'])) {
            $conditions.=' WHERE ';
            foreach ($options['condition'] as $key => $con) {
                $conditions.=$key . '=' . $con . ',';
            }

            $conditions = substr($conditions, 0, (strlen($conditions) - 1));
        }

        if (isset($_GET['search'])) {
            $conditions = ' WHERE ';
            $conditions.= $_GET['column'] . ' LIKE "' . $_GET['q'] . '%" ';
        }

        //parse sorting 
        $sort = '';

        if (!empty($options['sort'])) {
            $sort.=' ORDER BY ';
            foreach ($options['sort'] as $k => $st) {
                $sort.=$k . ' ' . $st . ',';
            }

            $sort = substr($sort, 0, (strlen($sort) - 1));
        }

        if (isset($_GET['sort'])) {
            $sort = ' ORDER BY ' . $_GET['sort'] . ' ' . strtoupper($_GET['type']);
        }

        //final query
        $sql = "SELECT " . $columns . " FROM " . $table . "  " . $conditions . " " . $sort . " limit " . $count . ", " . $limit;

        //echo $sql;

        $sth = $dbh->query($sql);
        $sth->setFetchMode(PDO::FETCH_ASSOC);

        $result = array();

        while ($row = $sth->fetch()) {
            array_push($result, $row);
        }

        //count total
        $c = 'SELECT COUNT(*) as numpage FROM ' . $table . "  " . $conditions;
        $sth = $dbh->query($c);
        $sth->setFetchMode(PDO::FETCH_NUM);
        $r = $sth->fetch();

        $numpage = ceil($r[0] / $limit);
        ?>

        <style type="text/css">

            .asc:after{
                content: "\e314"
            } 
            .desc:after{
                content: "\e315"
            } 
        </style>

        <table class="table table-bordered" id="<?php echo ($options['id'] != "") ? $options['id'] : $table . '-grid' ?>">
            <tr>
                <?php
                $pagisorturl = '';

                if (isset($_GET['sort']) && isset($_GET['type'])) {
                    $pagisorturl = '&sort=' . $_GET['sort'] . '&type=' . $_GET['type'];
                }

                $searchurl = '';
                if (isset($_GET['search']) && isset($_GET['column']) && isset($_GET['q'])) {
                    $searchurl = '&search=' . $_GET['search'] . '&column=' . $_GET['column'] . '&q=' . $_GET['q'];
                }

                $sortType = 'asc';
                $sortTypeClass = '';

                if (isset($_GET['type'])) {
                    if ($_GET['type'] == 'asc') {
                        $sortType = 'desc';
                        $sortTypeClass = 'desc';
                    } elseif ($_GET['type'] == 'desc') {
                        $sortType = 'asc';
                        $sortTypeClass = 'asc';
                    }
                }

                $urlsort = 0;
                if (isset($_GET['sort'])) {
                    $urlsort = $_GET['sort'];
                }

                foreach ($options['column'] as $col) {
                    ?>

                    <td class="<?php
                    if ($col == $urlsort) {
                        echo $sortTypeClass;
                    };
                    ?>"><a href="<?php echo $view; ?>?<?php echo $options['urlvariable'] ?>=<?php
                            if (isset($_GET[$options['urlvariable']])) {
                                echo $_GET[$options['urlvariable']];
                            } else {
                                echo 1;
                            };
                            ?>&sort=<?php echo $col ?>&type=<?php echo $sortType . $searchurl ?>"><?php echo strtoupper(str_replace("_", " ", $col)); ?></a>
                    </td>
                    <?php
                }
                ?>
                <td>
                    &nbsp;
                </td>
            </tr>
            <!--start search option-->
            <?php
            if (isset($options['search'])) {
                ?>
                <tr>
                    <?php
                    foreach ($options['column'] as $col) {
                        ?>
                        <td>
                            <form action="<?php echo $view; ?>" method="GET">
                                <input type="hidden" name="search" value="1"/>
                                <input type="hidden" name="column" value="<?php echo $col ?>"/>
                                <input type="text" name="q" class="form-control"/>
                            </form>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            <!--end search option-->

            <?php
            foreach ($result as $li) {
                ?>
                <tr>
                    <?php
                    foreach ($options['column'] as $col) {
                        ?>
                        <td><?php echo $li[$col] ?></td>
                        <?php
                    }
                    ?>
                    <td>
                        <?php
                        if (!empty($options['AbuttonColumn'])) {
                            foreach ($options['AbuttonColumn'] as $btn) {
                                ?>
                                <a class="<?php echo $btn['class'] ?> btn-sm" href="<?php echo $btn['url'] ?>?id=<?php echo $li[$options['column'][0]] ?>"><?php echo $btn['label'] ?></a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <p class="pull pull-left">
            Showing page 
            <?php
            if (isset($_GET[$options['urlvariable']])) {
                echo $_GET[$options['urlvariable']];
            } else {
                echo 1;
            }
            ?>
            of 

            <?php
            echo $numpage;
            ?>

        </p>

        <?php
        //if (sizeof($result) > 5) {
        ?>
        <ul class="pagination pull pull-right">
            <li><a href="<?php echo $view ?>?<?php echo $options['urlvariable'] ?>=1">First</a></li>

            <?php
            $start = 1;
            $end = 0;

            if ($numpage > 5) {
                $end = 5;
            } else {
                $end = $numpage;
            }
            if (isset($_GET[$options['urlvariable']]) && $_GET[$options['urlvariable']] > 3) {
                $start = $_GET[$options['urlvariable']] - 2;
                $end = $_GET[$options['urlvariable']] + 2;
            }

            if ($end >= $numpage) {
                $end = $numpage;
                //$start = $_GET['page'] - 4;
            }




            for ($i = $start; $i <= $end; $i++) {
                ?>
                <li <?php
                if (isset($_GET[$options['urlvariable']]) && $_GET[$options['urlvariable']] == $i) {
                    echo 'class="active"';
                }
                ?>>
                    <a href="<?php echo $view ?>?<?php echo $options['urlvariable'] ?>=<?php echo $i . $pagisorturl . $searchurl ?>"><?php echo $i ?></a></li>
                <?php
            }
            ?>
            <li><a href="<?php echo $view ?>?<?php echo $options['urlvariable'] ?>=<?php echo $numpage; ?>">Last</a></li>
        </ul>

        <?php
        //}
    }

    public function debugPrint($obj) {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
    }

}
