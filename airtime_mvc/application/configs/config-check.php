<?php
/*
 * We only get here after setup, or if there's an error in the configuration.
 *
 * Display a table to the user showing the necessary dependencies
 * (both PHP and binary) and the status of any application services,
 * along with steps to fix them if they're not found or misconfigured.
 */

$phpDependencies = checkPhpDependencies();
$externalServices = checkExternalServices();
$zend = $phpDependencies["zend"];
$postgres = $phpDependencies["postgres"];

$database =      $externalServices["database"];
$rabbitmq =      $externalServices["rabbitmq"];

$pypo =          $externalServices["pypo"];
$liquidsoap =    $externalServices["liquidsoap"];
$mediamonitor = $externalServices["media-monitor"];

$r1 = array_reduce($phpDependencies, "booleanReduce", true);
$r2 = array_reduce($externalServices, "booleanReduce", true);
$result = $r1 && $r2;
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css">
        <link rel="stylesheet" type="text/css" href="css/setup/config-check.css">
    </head>
    <style>
        /* 
            This is here because we're using the config-check css for 
            both this page and the system status page
         */
        html {
            background-color: #f5f5f5;
        }
        
        body {
            padding: 2em;
            min-width: 600px;
            text-align: center;
            margin: 3em ;
            border: 1px solid lightgray;
            border-radius: 5px;
        }
    </style>

    <body>
        <h2>
            <img class="logo" src="css/images/airtime_logo_jp.png" /><br/>
            <strong>Configuration Checklist</strong>
        </h2>

        <?php
        if (!$result) {
            ?>
            <br/>
            <h3 class="error">Looks like something went wrong!</h3>
            <p>
                Take a look at the checklist below for possible solutions. If you're tried the suggestions and are
                still experiencing issues, come
                <a href="https://forum.sourcefabric.org/">visit our forums</a>
                or <a href="http://www.sourcefabric.org/en/airtime/manuals/">check out the manual</a>.
            </p>
        <?php
        } else {
            ?>
            <p>
                Your Airtime station is up and running! Get started by logging in with the default username and password: admin/admin
            </p>
            <button onclick="location = location.pathname;">Log in to Airtime!</button>
        <?php
        }
        ?>


        <table class="table">
            <thead>
                <tr>
                    <th class="component">
                        Component
                    </th>
                    <th class="description">
                        <strong>Description</strong>
                    </th>
                    <th class="solution">
                        <strong>Status or Solution</strong>
                    </th>
                </tr>
            </thead>
        </table>

        <div class="checklist">
            <table class="table table-striped">
                <caption class="caption">
                    PHP Dependencies
                </caption>
                <tbody>
                    <tr class="<?=$zend ? 'success' : 'danger';?>">
                        <td class="component">
                            Zend
                        </td>
                        <td class="description">
                            Zend MVC Framework
                        </td>
                        <td class="solution <?php if ($zend) {echo 'check';?>">
                            <?php
                                } else {
                                    ?>">
                                    <b>Ubuntu</b>: try running <code>sudo apt-get install libzend-framework-php</code>
                                    <br/><b>Debian</b>: try running <code>sudo apt-get install zendframework</code>
                                <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <tr class="<?=$postgres ? 'success' : 'danger';?>">
                        <td class="component">
                            Postgres
                        </td>
                        <td class="description">
                            PDO and PostgreSQL libraries
                        </td>
                        <td class="solution <?php if ($postgres) {echo 'check';?>">
                            <?php
                                } else {
                                    ?>">
                                    Try running <code>sudo apt-get install php5.6-pgsql</code>
                                <?php
                                }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-striped">
                <caption class="caption">
                    External Services
                </caption>
                <tbody>
                <tr class="<?=$database ? 'success' : 'danger';?>">
                    <td class="component">
                        Database
                    </td>
                    <td class="description">
                        Database configuration for Airtime
                    </td>
                    <td class="solution <?php if ($database) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Make sure you aren't missing any of the Postgres dependencies in the table above.
                            If your dependencies check out, make sure your database configuration settings in
                            <code>/etc/airtime.conf</code> are correct and the Airtime database was installed correctly.
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$rabbitmq ? 'success' : 'danger';?>">
                    <td class="component">
                        RabbitMQ
                    </td>
                    <td class="description">
                        RabbitMQ configuration for Airtime
                    </td>
                    <td class="solution <?php if ($rabbitmq) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Make sure RabbitMQ is installed correctly, and that your settings in /etc/airtime/airtime.conf
                            are correct. Try using <code>sudo rabbitmqctl list_users</code> and <code>sudo rabbitmqctl list_vhosts</code>
                            to see if the airtime user (or your custom RabbitMQ user) exists, then checking that 
                            <code>sudo rabbitmqctl list_exchanges</code> contains entries for airtime-media-monitor, airtime-pypo, 
                            and airtime-uploads.
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$mediamonitor ? 'success' : 'danger';?>">
                    <td class="component">
                        Media Monitor
                    </td>
                    <td class="description">
                        Airtime media-monitor service
                    </td>
                    <td class="solution <?php if ($mediamonitor) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Check that the airtime-media-monitor service is installed correctly in <code>/etc/init</code>, 
                            and ensure that it's running with
                            <br/><code>initctl list | grep airtime-media-monitor</code><br/>
                            If not, try running <code>sudo service airtime-media-monitor start</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$pypo ? 'success' : 'danger';?>">
                    <td class="component">
                        Pypo
                    </td>
                    <td class="description">
                        Airtime playout service
                    </td>
                    <td class="solution <?php if ($pypo) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Check that the airtime-playout service is installed correctly in <code>/etc/init</code>, 
                            and ensure that it's running with
                            <br/><code>initctl list | grep airtime-playout</code><br/>
                            If not, try running <code>sudo service airtime-playout restart</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr class="<?=$liquidsoap ? 'success' : 'danger';?>">
                    <td class="component">
                        Liquidsoap
                    </td>
                    <td class="description">
                        Airtime liquidsoap service
                    </td>
                    <td class="solution <?php if ($liquidsoap) {echo 'check';?>">
                        <?php
                        } else {
                            ?>">
                            Check that the airtime-liquidsoap service is installed correctly in <code>/etc/init</code>, 
                            and ensure that it's running with
                            <br/><code>initctl list | grep airtime-liquidsoap</code><br/>
                            If not, try running <code>sudo service airtime-liquidsoap restart</code>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <h3>
                PHP Extension List
            </h3>
            <p>
                <?php
                    global $extensions;
                    $first = true;
                    foreach ($extensions as $ext) {
                        if (!$first) {
                            echo " | ";
                        } else {
                            $first = false;
                        }
                        echo $ext;
                    }
                ?>
            </p>
        </div>
