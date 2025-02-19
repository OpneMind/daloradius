<?php
/*
 *********************************************************************************************************
 * daloRADIUS - RADIUS Web Platform
 * Copyright (C) 2007 - Liran Tal <liran@enginx.com> All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *********************************************************************************************************
 *
 * Description:    this extension creates a pie chart of
 *                 the comparison of hotspots per unique users
 *
 * Authors:        Liran Tal <liran@enginx.com>
 *                 Filippo Lauria <filippo.lauria@iit.cnr.it>
 *
 *********************************************************************************************************
 */

    include('checklogin.php');

    include('opendb.php');
    include('libchart/classes/libchart.php');

    $chart = new PieChart(800, 600);
    $dataSet = new XYDataSet();

    $sql = sprintf("SELECT hs.name, COUNT(DISTINCT(UserName)), COUNT(radacctid),
                           AVG(AcctSessionTime), SUM(AcctSessionTime)
                      FROM %s AS ra, %s AS hs
                     WHERE ra.calledstationid = hs.mac
                     GROUP BY hs.name", $configValues['CONFIG_DB_TBL_RADACCT'],
                                        $configValues['CONFIG_DB_TBL_DALOHOTSPOTS']);
    $res = $dbSocket->query($sql);

    while ($row = $res->fetchRow()) {
        $label = sprintf("%s (%s users)", $row[0], $row[1]);
        $value = intval($row[1]);
        
        $point = new Point($label, $value);
        $dataSet->addPoint($point);
    }

    include('closedb.php');
    
    header("Content-type: image/png");
    $chart->setTitle("Distribution of Unique users per Hotspot");
    $chart->setDataSet($dataSet);
    $chart->render();

?>
