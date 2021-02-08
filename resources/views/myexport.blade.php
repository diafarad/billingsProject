<table class="table">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <?php
                foreach ($BEF as $b) {
                    if ($b->n>0) {
            ?>
                        <th colspan="<?php echo $b->n*3; ?>">BEF<th>
            <?php
                    }
                }
                foreach ($SFD as $s) {
                    if ($s->n>0) {
            ?>
                        <th colspan="<?php echo $s->n*3; ?>">SFD<th>
            <?php
                    }
                }
            ?>
        </tr>
    <tr>
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
            $res = \Illuminate\Support\Facades\DB::select('SELECT COALESCE(rv.n,0) as "TotalRapportVide", COALESCE(rd.n,0) as "TotalRapportData", COALESCE(COUNT(DISTINCT(b.user_name)),0) as "TotalNbUser"
                                                                        FROM billing_stats b, (SELECT subscriber_name as "s", COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$inst->name.'"
                                                                                                   AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                                   GROUP BY subscriber_name) rv,
                                                                                              (SELECT subscriber_name as "s", COUNT(usage_name) as "n"
                                                                                                   FROM billing_stats
                                                                                                   WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND subscriber_name = "'.$inst->name.'"
                                                                                                   AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                                   GROUP BY subscriber_name) rd
                                                                        WHERE b.subscriber_name = "'.$inst->name.'"
                                                                        AND b.subscriber_name = rd.s AND b.subscriber_name = rv.s
                                                                        AND b.stats_date BETWEEN "'.$from.'" AND "'.$to.'"
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
                $rapportVide = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "vide"
                                                                               FROM billing_stats
                                                                               WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$ins->name.'"
                                                                               AND stats_date = "'.$date->d.'"
                                                                               GROUP BY stats_date');
                $rapportData = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "plus"
                                                                               FROM billing_stats
                                                                               WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND subscriber_name = "'.$ins->name.'"
                                                                               AND stats_date = "'.$date->d.'"
                                                                               GROUP BY stats_date');
                $nbreUser = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "user"
                                                                            FROM billing_stats
                                                                            WHERE (lower(usage_name) = "rapport de crédit bic civ plus" OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$ins->name.'"
                                                                            AND stats_date = "'.$date->d.'"
                                                                            GROUP BY stats_date');
                if(empty($rapportVide)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($rapportVide as $rv){
                ?>
                <td><?php echo $rv->vide; ?></td>
                <?php
                }
                }
                if(empty($rapportData)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($rapportData as $rd){
                ?>
                <td><?php echo $rd->plus; ?></td>
                <?php
                }
                }
                if(empty($nbreUser)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($nbreUser as $u){
                ?>
                <td><?php echo $u->user; ?></td>
                <?php
                }
                }
                ?>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
