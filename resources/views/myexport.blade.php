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

            $totalRvide = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$inst->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');

            $totalRdata = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$inst->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');

            $totalUser = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT user_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$inst->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');
            if(empty($totalRvide)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalRvide as $rv){
            ?>
            <td><?php echo $rv->n; ?></td>
            <?php
            }
            }
            if(empty($totalRdata)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalRdata as $rd){
            ?>
            <td><?php echo $rd->n; ?></td>
            <?php
            }
            }
            if(empty($totalUser)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalUser as $u){
            ?>
            <td><?php echo $u->n; ?></td>
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
                                                                               WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$ins->name.'"
                                                                               AND stats_date = "'.$date->d.'"
                                                                               GROUP BY stats_date');
                $nbreUser = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "user"
                                                                            FROM billing_stats
                                                                            WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$ins->name.'"
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
