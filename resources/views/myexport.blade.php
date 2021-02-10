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
        <?php
        if (!empty($lesBEF)) {
        ?>
            @foreach($lesBEF as $bef)
                <th colspan="3">{{ $bef->name }}</th>
            @endforeach
        <?php
        }
        if (!empty($lesSFD)) {
        ?>
            @foreach($lesSFD as $sfd)
                <th colspan="3">{{ $sfd->name }}</th>
            @endforeach
        <?php
        }
        ?>
    </tr>
    <tr>
        <th></th>
        <?php
        if (!empty($lesBEF)) {
        ?>
        @foreach($lesBEF as $bef)
            <th>RVide</th>
            <th>RData</th>
            <th>NbUser</th>
        @endforeach
        <?php
        }
        if (!empty($lesSFD)) {
        ?>
        @foreach($lesSFD as $sfd)
        <th>RVide</th>
        <th>RData</th>
        <th>NbUser</th>
        @endforeach
        <?php
        }
        ?>
    </tr>
    <tr>
        <th>TOTAL</th>
        <?php
            if (!empty($lesBEF)) {
        ?>
        @foreach($lesBEF as $bef)
        <?php
            ini_set('max_execution_time', 500);

            $totalRvideBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$bef->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');

            $totalRdataBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$bef->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');

            $totalUserBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT user_name) as "n"
                                                                    FROM billing_stats
                                                                    WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$bef->name.'"
                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                    GROUP BY subscriber_name');
            if(empty($totalRvideBEF)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalRvideBEF as $rvBEF){
            ?>
            <td><?php echo $rvBEF->n; ?></td>
            <?php
            }
            }
            if(empty($totalRdataBEF)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalRdataBEF as $rdBEF){
            ?>
            <td><?php echo $rdBEF->n; ?></td>
            <?php
            }
            }
            if(empty($totalUserBEF)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($totalUserBEF as $uBEF){
            ?>
            <td><?php echo $uBEF->n; ?></td>
            <?php
            }
            }
        ?>
        @endforeach
        <?php
            }
            if (!empty($lesSFD)) {
        ?>
        @foreach($lesSFD as $sfd)
            <?php
                ini_set('max_execution_time', 500);

                $totalRvideSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$sfd->name.'"
                                                                        AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                        GROUP BY subscriber_name');

                $totalRdataSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$sfd->name.'"
                                                                        AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                        GROUP BY subscriber_name');

                $totalUserSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT user_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$sfd->name.'"
                                                                        AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                        GROUP BY subscriber_name');
                if (empty($totalRvideSFD)) {
                ?>
                <td>0</td>
                <?php
                }else{
                    foreach ($totalRvideSFD as $rvSFD) {
                ?>
                        <td><?php echo $rvSFD->n; ?></td>
                <?php
                    }
                }
                if (empty($totalRdataSFD)) {
                ?>
                <td>0</td>
                <?php
                }else{
                    foreach ($totalRdataSFD as $rdSFD) {
                ?>
                        <td><?php echo $rdSFD->n; ?></td>
                <?php
                }
                }
                if (empty($totalUserSFD)) {
                ?>
                <td>0</td>
                <?php
                }else{
                    foreach ($totalUserSFD as $uSFD) {
                ?>
                        <td><?php echo $uSFD->n; ?></td>
                <?php
                }
                }
            ?>
        @endforeach
        <?php
            }
        ?>
    </tr>
    </thead>
    <tbody>
    @foreach($lesdates as $date)
        <tr>
            <td>{{ $date->d }}</td>
            <?php
            if (!empty($lesBEF)) {
            ?>
            @foreach($lesBEF as $bef)
            <?php
                ini_set('max_execution_time', 500);

                $rvideBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$bef->name.'"
                                                                        AND stats_date = "'.$date->d.'"
                                                                        GROUP BY stats_date');

                $rdataBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$bef->name.'"
                                                                        AND stats_date = "'.$date->d.'"
                                                                        GROUP BY stats_date');

                $userBEF = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT user_name) as "n"
                                                                        FROM billing_stats
                                                                        WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$bef->name.'"
                                                                        AND stats_date = "'.$date->d.'"
                                                                        GROUP BY stats_date');
                if(empty($rvideBEF)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($rvideBEF as $rvBEF){
                ?>
                <td><?php echo $rvBEF->n; ?></td>
                <?php
                }
                }
                if(empty($rdataBEF)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($rdataBEF as $rdBEF){
                ?>
                <td><?php echo $rdBEF->n; ?></td>
                <?php
                }
                }
                if(empty($userBEF)){
                ?>
                <td>0</td>
                <?php
                }
                else{
                foreach ($userBEF as $uBEF){
                ?>
                <td><?php echo $uBEF->n; ?></td>
                <?php
                }
                }
            ?>
            @endforeach
            <?php
                }
                if (!empty($lesSFD)) {
            ?>
            @foreach($lesSFD as $sfd)
                <?php
                    ini_set('max_execution_time', 500);

                    $rvideSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE lower(usage_name) = "rapport de crédit bic civ vide" AND subscriber_name = "'.$sfd->name.'"
                                                                            AND stats_date = "'.$date->d.'"
                                                                            GROUP BY stats_date');

                    $rdataSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" AND subscriber_name = "'.$sfd->name.'"
                                                                            AND stats_date = "'.$date->d.'"
                                                                            GROUP BY stats_date');

                    $userSFD = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT user_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide") AND subscriber_name = "'.$sfd->name.'"
                                                                            AND stats_date = "'.$date->d.'"
                                                                            GROUP BY stats_date');
                    if (empty($rvideSFD)) {
                    ?>
                    <td>0</td>
                    <?php
                    }else{
                        foreach ($rvideSFD as $rvSFD) {
                    ?>
                            <td><?php echo $rvSFD->n; ?></td>
                    <?php
                        }
                    }
                    if (empty($rdataSFD)) {
                    ?>
                    <td>0</td>
                    <?php
                    }else{
                        foreach ($rdataSFD as $rdSFD) {
                    ?>
                            <td><?php echo $rdSFD->n; ?></td>
                    <?php
                    }
                    }
                    if (empty($userSFD)) {
                    ?>
                    <td>0</td>
                    <?php
                    }else{
                        foreach ($userSFD as $uSFD) {
                    ?>
                            <td><?php echo $uSFD->n; ?></td>
                    <?php
                    }
                    }
                ?>
            @endforeach
            <?php
                }
            ?>
        </tr>
    @endforeach
    </tbody>
</table>
