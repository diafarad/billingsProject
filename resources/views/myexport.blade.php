    <table class="table">
    <thead>
    <tr class="table-primary">
        <th></th>
        @foreach($institutions as $inst)
            <th colspan="3">{{ $inst->name }}</th>
        @endforeach
    </tr>
    <tr>
        <th></th>
        @foreach($institutions as $inst)
            <th>RVide</th>
            <th>RData</th>
            <th>NbUser</th>
        @endforeach
    </tr>
    <tr>
        <th>TOTAL</th>
        @foreach($institutions as $inst)
            <?php
            ini_set('max_execution_time', 500);
            $res = \Illuminate\Support\Facades\DB::select('SELECT COALESCE(rv.n,0) as "TotalRapportVide", COALESCE(rd.n,0) as "TotalRapportData", COALESCE(COUNT(DISTINCT(user_name)),0) as "TotalNbUser"
                                                                        FROM billing_stats b, (SELECT subscriber_name as "s", COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$inst->name.'"
                                                                                                   AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                                                                                   GROUP BY subscriber_name) rv,
                                                                                              (SELECT subscriber_name as "s", COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND subscriber_name = "'.$inst->name.'"
                                                                                                   AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                                                                                   GROUP BY subscriber_name) rd
                                                                        WHERE b.subscriber_name = "'.$inst->name.'"
                                                                        AND b.subscriber_name = rd.s AND b.subscriber_name = rv.s
                                                                        AND b.stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                                                        GROUP BY rv.n, rd.n');


            if(empty($res)){
            ?>
            <th>0</th>
            <th>0</th>
            <th>0</th>
            <?php
            }
            else{

            foreach ($res as $r){
            ?>
            <th><?php echo $r->TotalRapportVide; ?></th>
            <th><?php echo $r->TotalRapportData; ?></th>
            <th><?php echo $r->TotalNbUser; ?></th>
            <?php
            }
            }
            ?>
        @endforeach
    </tr>
    </thead>
    <tbody>
        @foreach($lesdates as $date)
            <tr>
                <td>{{ $date->d }}</td>
                @foreach($institutions as $ins)
                    <?php
                    ini_set('max_execution_time', 500);
                        $res = \Illuminate\Support\Facades\DB::select('SELECT COALESCE(rv.n,0) as "RapportVide", COALESCE(rd.n,0) as "RapportData", COALESCE(COUNT(DISTINCT(user_name)),0) as "NbUser"
                                                                        FROM billing_stats b, (SELECT stats_date, COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$ins->name.'"
                                                                                                   AND stats_date = "'.$date->d.'"
                                                                                                   GROUP BY stats_date) rv,
                                                                                              (SELECT stats_date, COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND subscriber_name = "'.$ins->name.'"
                                                                                                            AND stats_date = "'.$date->d.'"
                                                                                                   GROUP BY stats_date) rd
                                                                        WHERE b.stats_date = rv.stats_date AND b.stats_date=rd.stats_date
                                                                        AND b.subscriber_name = "'.$ins->name.'"
                                                                        AND b.stats_date = "'.$date->d.'"
                                                                        GROUP BY rv.n, rd.n');


                        if(empty($res)){
                            ?>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <?php
                        }
                        else{

                        foreach ($res as $r){
                            ?>
                            <td><?php echo $r->RapportVide; ?></td>
                            <td><?php echo $r->RapportData; ?></td>
                            <td><?php echo $r->NbUser; ?></td>
                    <?php
                        }
                        }
                    ?>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
