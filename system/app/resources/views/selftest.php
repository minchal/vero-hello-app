<?php

$print_results = function ($results) {
    echo '<table><colgroup span="2" style="width: 50%" />';

    foreach ($results as $i) {
        echo '<tr><td>' . $i[0] . '</td>';
        if (isset($i[2])) {
            if ($i[2]) {
                echo '<td class="ok">' . ($i[1] ? $i[1] : 'YES') . '</td></tr>';
            } else {
                echo '<td class="bad">' . ($i[1] ? $i[1] : 'NO') . '</td></tr>';
            }
        } else {
            echo '<td>' . $i[1] . '</td></tr>';
        }
    }
    echo '</table>';
};

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    
    <script src="public/js/jquery.js"></script>
    <script>
        $(function() {
            $('.group').each(function() {
                var $this = $(this);
                $this.find('h2').click(function() {
                    $this.toggleClass('hidden');
                });
            });
        });
    </script>
    
    <style type="text/css">
        body {
            padding: 0; margin: 0;
            font: 10pt normal Arial, Verdana, sans-serif;
            background: #eee; color: #333;
        }
        h1 {font-size: 25px; padding: 0; margin: 0 0 10px 0; color: #666;}
        h2 {font-size: 18px; padding: 0; margin: 0 0 10px 0; }
        p {margin: 0; padding: 0 0 1em 0;}
        a {text-decoration: none;}
        a:hover {text-decoration: underline;}
        hr {
            margin: 0 0 20px 0; border: none;
            border-bottom: solid 1px #777;
        }
        #content {
            width: 850px; padding: 15px;
            margin: 50px auto 0 auto;
            background: #fff; border: solid 1px #777;
        }
        #foot {
            width: 850px; padding: 15px;
            margin: 0 auto 20px;
            font-size: 9pt; background: #fff;
            border: solid 1px #777; border-top: none;
        }
        
        table {
            width: 100%;
            margin: 0 0 20px 0;
            border-collapse: collapse;
        }
        td, th {
            border:solid 1px #aaa;
            padding: 3px 5px;
        }
        th {
            font-weight: bold;
            text-align: left;
        }
        .ok {color: #178e17;}
        .bad {color: #f43636;}
        
        .group h2:after {
            content: ' [-]';
            color: blue;
            font-weight: normal;
            font-size: 17px;
        }
        .group h2:hover { cursor: pointer; }
        .group h2:hover:after { color: red; }
        .group.hidden h2:after { content: ' [+]'; }
        .group.hidden table { display: none; }
    </style>
</head>
<body>
    <div id="content">
        <h1><?= $title ?></h1>
        <hr />
        
        <?php
        foreach ($tests as $group => $results) {
            echo '<div class="group">';
            echo '<h2>' . $group . '</h2>';
            
            $print_results($results);
            
            echo '</div>';
        }
        ?>
        
        <div class="group hidden">
            <h2>Locales</h2>
            <table>
                <thead>
                    <th>Language</th>
                    <th>Locales</th>
                </thead>
                <?php
                foreach ($locales as $lang => $loc) {
                    ?>
                        <tr>
                            <td><a href="?lang=<?=$lang?>"><?=$lang?></a></td>
                            <td><?=print_r($loc, true)?></td>
                        </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        
        <div class="group hidden">
            <h2>Autoloader</h2>
            <table>
                <thead>
                    <th>Namespace prefix</th>
                    <th>Path</th>
                </thead>
                <?php
                foreach ($autoload as $prefix => $path) {
                    echo '<tr><td>'.$prefix.'</td><td>'.$path.'</td></tr>';
                }
                ?>
            </table>
        </div>
        
        <div class="group hidden">
            <h2>Routing</h2>
            <table>
                <thead>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Prefix</th>
                </thead>
                <?php
                foreach ($routes as $id => $route) {
                    echo '<tr><td>'.$id.'</td><td>'.$route.'</td><td>'.$route->getPrefix().'</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <div id="foot">
        <?= $signature ?>
        <?= ($debug ? '<pre>' . $debug . '</pre>' : '') ?>
    </div>
</body>
</html>
