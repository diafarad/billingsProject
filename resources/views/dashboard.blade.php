<?php
        if (!empty($lesBEF)) {
    ?>
<table class="table">
    <thead>
    <tr>
        <th></th>
        <th>Jour</th>
        <th>{{ $jour }}</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th colspan="5">Résumé des consultations journalières (Banques et Etablissements Financiers)</th>
        <th colspan="6">Résumé des consultations mensuelles (Banques et Etablissements Financiers)</th>
    </tr>
    <tr>
        <th></th>
        <th>BEF</th>
        <th>Nbre d'utilisateurs habilités</th>
        <th>Nbre d'utilisateurs ayant consulté</th>
        <th>Nbre de rapports consultés</th>
        <th>% Rapports avec données</th>
        <th>Objectifs journaliers</th>
        <th>Nbre d'utilisateurs ayant consulté</th>
        <th>Volume moyen journalier</th>
        <th>% Rapports avec données</th>
        <th>Nbre de rapports consultés du  mois</th>
        <th>Objectifs mensuels</th>
        <th>Variance  M/M-1</th>
    </tr>
    </thead>
    <tbody>
        @foreach($lesBEF as $bef)
            <tr>
                <td></td>
                <td>{{$bef->name}}</td>
                <?php
                ini_set('max_execution_time', 500);
                $nbUserAvailable = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userAv"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$bef->name.'"
                                                                                GROUP BY subscriber_name');
                $nbUserConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userCons"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$bef->name.'"
                                                                            AND stats_date = "'.$today.'"
                                                                            GROUP BY subscriber_name');
                $nbRapportConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportCons"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$bef->name.'"
                                                                                AND (lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date = "'.$today.'"
                                                                                GROUP BY subscriber_name');
                $prcentRapportData = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "Pourcentage"
                                                                                FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$bef->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                                    AND stats_date = "'.$today.'"
                                                                                    GROUP BY subscriber_name) b1,
                                                                                    (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$bef->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                                    AND stats_date = "'.$today.'"
                                                                                    GROUP BY subscriber_name) b2
                                                                                WHERE b1.subscriber_name=b2.subscriber_name');
                $nbUserMoisConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userMoisCons"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$bef->name.'"
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                GROUP BY subscriber_name');
                $volumeMoyen = \Illuminate\Support\Facades\DB::select('SELECT (COUNT(usage_name)/20) as "volMoy"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$bef->name.'"
                                                                                AND (lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit" OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                GROUP BY subscriber_name');
                $prcentRapportDataMois = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "PourcentageMois"
                                                                                FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$bef->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                    GROUP BY subscriber_name) b1,
                                                                                    (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$bef->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                    GROUP BY subscriber_name) b2
                                                                                WHERE b1.subscriber_name=b2.subscriber_name');
                $nbRapportConsultMois = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportConsMois"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$bef->name.'"
                                                                                AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                GROUP BY subscriber_name');
                $variance = \Illuminate\Support\Facades\DB::select('SELECT (((m1.n-n1.n)/n1.n)*100) as "result"
                                                                    FROM (SELECT subscriber_name, COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$bef->name.'"
                                                                            AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$from.'" AND "'.$aaaa.'-'.$mm.'-'.$jj.'"
                                                                            GROUP BY subscriber_name) as m1,
                                                                        (SELECT subscriber_name, COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$bef->name.'"
                                                                            AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$aaaa.'-'.$mPrec.'-01" AND "'.$aaaa.'-'.$mPrec.'-30"
                                                                            GROUP BY subscriber_name) as n1
                                                                    WHERE m1.subscriber_name=n1.subscriber_name');
                if(empty($nbUserAvailable)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserAvailable as $nbuser){
                ?>
                <td><?php echo $nbuser->userAv; ?></td>
                <?php
                }
                }
                if (empty($nbUserConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserConsult as $nbuser){
                ?>
                <td><?php echo $nbuser->userCons; ?></td>
                <?php
                }
                }
                if (empty($nbRapportConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbRapportConsult as $nbrap){
                ?>
                <td><?php echo $nbrap->rapportCons; ?></td>
                <?php
                }
                }
                if (empty($prcentRapportData)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($prcentRapportData as $prct){
                ?>
                <td><?php echo number_format($prct->Pourcentage,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                ?>
                <td>10</td>
                <?php
                if (empty($nbUserMoisConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserMoisConsult as $userMois){
                ?>
                <td><?php echo $userMois->userMoisCons; ?></td>
                <?php
                }
                }
                if (empty($volumeMoyen)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($volumeMoyen as $vol){
                ?>
                <td><?php echo $vol->volMoy; ?></td>
                <?php
                }
                }
                if (empty($prcentRapportDataMois)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($prcentRapportDataMois as $prctMois){
                ?>
                <td><?php echo number_format($prctMois->PourcentageMois,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                if (empty($nbRapportConsultMois)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbRapportConsultMois as $rpMois){
                ?>
                <td><?php echo $rpMois->rapportConsMois; ?></td>
                <?php
                }
                }
                ?>
                <td>25</td>
                <?php
                if (empty($variance)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($variance as $v){
                ?>
                <td><?php echo number_format($v->result,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                ?>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td>TOTAL</td>
            <?php
            $nbUserAvailableTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.userAv) as "res"
                                                                            FROM (SELECT COUNT(DISTINCT(b.user_name)) as "userAv"
                                                                            FROM billing_stats b, subscribers s
                                                                            WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                            AND lower(s.sector) = "banque"
                                                                            AND b.subscriber_name=s.name
                                                                            GROUP BY b.subscriber_name) t');
            if (empty($nbUserAvailableTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{

            foreach ($nbUserAvailableTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
        </tr>
        <tr>
            <td></td>
            <td>VARIANCE P/P-1</td>
        </tr>
    </tbody>
</table>
<?php } ?>
<?php
        if (!empty($lesSFD)) {
?>
<table class="table">
    <thead>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th></th>
            <th></th>
            <th colspan="5">Résumé des consultations journalières (SFD Art 44)</th>
            <th colspan="6">Résumé des consultations mensuelles (SFD Art 44)</th>
        </tr>
        <tr>
            <th></th>
            <th>SFD</th>
            <th>Nbre d'utilisateurs habilités</th>
            <th>Nbre d'utilisateurs ayant consulté</th>
            <th>Nbre de rapports consultés</th>
            <th>% Rapports avec données</th>
            <th>Objectifs journaliers</th>
            <th>Nbre d'utilisateurs ayant consulté</th>
            <th>Volume moyen journalier</th>
            <th>% Rapports avec données</th>
            <th>Nbre de rapports consultés du  mois</th>
            <th>Objectifs mensuels</th>
            <th>Variance  M/M-1</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lesSFD as $sfd)
            <tr>
                <td></td>
                <td>{{$sfd->name}}</td>
                <?php
                ini_set('max_execution_time', 500);
                $nbUserAvailable = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userAv"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$sfd->name.'"
                                                                                GROUP BY subscriber_name');
                $nbUserConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userCons"
                                                                            FROM billing_stats b, subscribers s
                                                                            WHERE b.subscriber_name = "'.$sfd->name.'"
                                                                            AND b.subscriber_name=s.name
                                                                            AND b.stats_date = "'.$today.'"
                                                                            AND ((lower(b.usage_name)="rapport de crédit bic civ plus" AND lower(b.detail_name)="données sur le crédit") OR lower(b.usage_name)="rapport de crédit bic civ vide")
                                                                        GROUP BY b.subscriber_name');
                $nbRapportConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportCons"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$sfd->name.'"
                                                                                AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date = "'.$today.'"
                                                                                GROUP BY subscriber_name');
                $prcentRapportData = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "Pourcentage"
                                                                                FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$sfd->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                                    AND stats_date = "'.$today.'"
                                                                                    GROUP BY subscriber_name) b1,
                                                                                    (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$sfd->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                                    AND stats_date = "'.$today.'"
                                                                                    GROUP BY subscriber_name) b2
                                                                                WHERE b1.subscriber_name=b2.subscriber_name');
                $nbUserMoisConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userMoisCons"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$sfd->name.'"
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                GROUP BY subscriber_name');
                $volumeMoyen = \Illuminate\Support\Facades\DB::select('SELECT (COUNT(usage_name)/20) as "volMoy"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$sfd->name.'"
                                                                                AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                GROUP BY subscriber_name');
                $prcentRapportDataMois = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "PourcentageMois"
                                                                                FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$sfd->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                    GROUP BY subscriber_name) b1,
                                                                                    (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                                    FROM billing_stats
                                                                                    WHERE subscriber_name = "'.$sfd->name.'"
                                                                                    AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                                    AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                    GROUP BY subscriber_name) b2
                                                                                WHERE b1.subscriber_name=b2.subscriber_name');
                $nbRapportConsultMois = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportConsMois"
                                                                                FROM billing_stats
                                                                                WHERE subscriber_name = "'.$sfd->name.'"
                                                                                AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name)="données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                                AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                GROUP BY subscriber_name');
                $variance = \Illuminate\Support\Facades\DB::select('SELECT (((m1.n-n1.n)/n1.n)*100) as "result"
                                                                    FROM (SELECT subscriber_name, COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$sfd->name.'"
                                                                            AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$from.'" AND "'.$aaaa.'-'.$mm.'-'.$jj.'"
                                                                            GROUP BY subscriber_name) as m1,
                                                                        (SELECT subscriber_name, COUNT(usage_name) as "n"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$sfd->name.'"
                                                                            AND ((lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit") OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$aaaa.'-'.$mPrec.'-01" AND "'.$aaaa.'-'.$mPrec.'-30"
                                                                            GROUP BY subscriber_name) as n1
                                                                    WHERE m1.subscriber_name=n1.subscriber_name');
                if(empty($nbUserAvailable)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserAvailable as $nbuser){
                ?>
                <td><?php echo $nbuser->userAv; ?></td>
                <?php
                }
                }
                if (empty($nbUserConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserConsult as $nbuser){
                ?>
                <td><?php echo $nbuser->userCons; ?></td>
                <?php
                }
                }
                if (empty($nbRapportConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbRapportConsult as $nbrap){
                ?>
                <td><?php echo $nbrap->rapportCons; ?></td>
                <?php
                }
                }
                if (empty($prcentRapportData)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($prcentRapportData as $prct){
                ?>
                <td><?php echo number_format($prct->Pourcentage,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                ?>
                <td>10</td>
                <?php
                if (empty($nbUserMoisConsult)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbUserMoisConsult as $userMois){
                ?>
                <td><?php echo $userMois->userMoisCons; ?></td>
                <?php
                }
                }
                if (empty($volumeMoyen)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($volumeMoyen as $vol){
                ?>
                <td><?php echo $vol->volMoy; ?></td>
                <?php
                }
                }
                if (empty($prcentRapportDataMois)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($prcentRapportDataMois as $prctMois){
                ?>
                <td><?php echo number_format($prctMois->PourcentageMois,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                if (empty($nbRapportConsultMois)){
                ?>
                <td>0</td>
                <?php
                }
                else{

                foreach ($nbRapportConsultMois as $rpMois){
                ?>
                <td><?php echo $rpMois->rapportConsMois; ?></td>
                <?php
                }
                }
                ?>
                <td>25</td>
                <?php
                if (empty($variance)){
                ?>
                <td>0.00%</td>
                <?php
                }
                else{

                foreach ($variance as $v){
                ?>
                <td><?php echo number_format($v->result,2,'.',' ').'%'; ?></td>
                <?php
                }
                }
                ?>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td>TOTAL</td>
            <?php
            $nbUserAvailableTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.userAv) as "res"
                                                                            FROM (SELECT COUNT(DISTINCT(b.user_name)) as "userAv"
                                                                            FROM billing_stats b, subscribers s
                                                                            WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                            AND lower(s.sector) = "autre sfd"
                                                                            AND b.subscriber_name=s.name
                                                                            GROUP BY b.subscriber_name) t');
            if (empty($nbUserAvailableTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{

            foreach ($nbUserAvailableTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $nbUserConsultTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.userAv) as "res"
                                                                            FROM (SELECT COUNT(DISTINCT(b.user_name)) as "userAv"
                                                                            FROM billing_stats b, subscribers s
                                                                            WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                            AND lower(s.sector) = "autre sfd"
                                                                            AND ((lower(b.usage_name)="rapport de crédit bic civ plus" AND lower(b.detail_name)="données sur le crédit") OR lower(b.usage_name)="rapport de crédit bic civ vide")
                                                                            AND b.subscriber_name=s.name
                                                                            AND b.stats_date = "'.$today.'"
                                                                            GROUP BY b.subscriber_name) t');
            if (empty($nbUserConsultTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{

            foreach ($nbUserConsultTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $nbRapportTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.rapportCons) as "res"
                                                                            FROM (SELECT COUNT(usage_name) as "rapportCons"
                                                                                    FROM billing_stats b, subscribers s
                                                                                    WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                    AND lower(s.sector) = "autre sfd"
                                                                                    AND b.subscriber_name=s.name
                                                                                    AND ((lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name)="données sur le crédit") OR lower(b.usage_name) = "rapport de crédit bic civ vide")
                                                                                    AND b.stats_date = "'.$today.'"
                                                                                    GROUP BY b.subscriber_name) t');
            if (empty($nbRapportTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{

            foreach ($nbRapportTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $prcentRapportDataTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.Pourcentage) as "res"
                                                                            FROM (SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "Pourcentage"
                                                                                    FROM (SELECT b.subscriber_name, COUNT(b.usage_name) as "nbData"
                                                                                        FROM billing_stats b, subscribers s
                                                                                        WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                        AND b.subscriber_name = s.name
                                                                                        AND lower(s.sector)="autre sfd"
                                                                                        AND lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name) = "données sur le crédit"
                                                                                        AND b.stats_date = "'.$today.'"
                                                                                        GROUP BY b.subscriber_name) b1,
                                                                                        (SELECT b.subscriber_name, COUNT(b.usage_name) as "nbVide"
                                                                                        FROM billing_stats b, subscribers s
                                                                                        WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                        AND b.subscriber_name = s.name
                                                                                        AND lower(s.sector)="autre sfd"
                                                                                        AND lower(b.usage_name) = "rapport de crédit bic civ vide"
                                                                                        AND b.stats_date = "'.$today.'"
                                                                                        GROUP BY b.subscriber_name) b2
                                                                                    WHERE b1.subscriber_name=b2.subscriber_name) t');
            if (empty($prcentRapportDataTotal)){
            ?>
            <td>0.00%</td>
            <?php
            }
            else{

            foreach ($prcentRapportDataTotal as $n){
            ?>
            <td><?php echo number_format($n->res,2,'.',' ').'%'; ?></td>
            <?php
            }
            }
            ?>
            <td>100</td>
            <?php
            $nbUserMoisConsultTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.userMoisCons) as "res"
                                                                            FROM (SELECT COUNT(DISTINCT(b.user_name)) as "userMoisCons"
                                                                                    FROM billing_stats b, subscribers s
                                                                                    WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                    AND lower(s.sector) = "autre sfd"
                                                                                    AND b.subscriber_name=s.name
                                                                                    AND ((lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name)="données sur le crédit") OR lower(b.usage_name) = "rapport de crédit bic civ vide")
                                                                                    AND b.stats_date BETWEEN "'.$from.'" AND "'.$today.'"
                                                                                    GROUP BY b.subscriber_name) t');
            if (empty($nbUserMoisConsultTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{

            foreach ($nbUserMoisConsultTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $volumeMoyenTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.volMoy) as "res"
                                                                            FROM (SELECT (COUNT(b.usage_name)/20) as "volMoy"
                                                                                    FROM billing_stats b, subscribers s
                                                                                    WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                    AND lower(s.sector) = "autre sfd"
                                                                                    AND b.subscriber_name=s.name
                                                                                    AND ((lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name) = "données sur le crédit") OR lower(b.usage_name) = "rapport de crédit bic civ vide")
                                                                                    AND b.stats_date BETWEEN "'.$from.'" AND "'.$today.'"
                                                                                    GROUP BY b.subscriber_name) t');
            if (empty($volumeMoyenTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($volumeMoyenTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $nbRapportDataMoisTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.PourcentageMois) as "res"
                                                                                FROM (SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "PourcentageMois"
                                                                                        FROM (SELECT b.subscriber_name, COUNT(b.usage_name) as "nbData"
                                                                                        FROM billing_stats b, subscribers s
                                                                                        WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                        AND b.subscriber_name = s.name
                                                                                        AND lower(s.sector)="autre sfd"
                                                                                        AND lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name) = "données sur le crédit"
                                                                                        AND b.stats_date BETWEEN "'.$from.'" AND "'.$today.'"
                                                                                        GROUP BY b.subscriber_name) b1,
                                                                                        (SELECT b.subscriber_name, COUNT(b.usage_name) as "nbVide"
                                                                                        FROM billing_stats b, subscribers s
                                                                                        WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                        AND b.subscriber_name = s.name
                                                                                        AND lower(s.sector)="autre sfd"
                                                                                        AND lower(b.usage_name) = "rapport de crédit bic civ vide"
                                                                                        AND b.stats_date BETWEEN "'.$from.'" AND "'.$today.'"
                                                                                        GROUP BY b.subscriber_name) b2
                                                                                        WHERE b1.subscriber_name=b2.subscriber_name) t');
            if (empty($nbRapportDataMoisTotal)){
            ?>
            <td>0.00%</td>
            <?php
            }
            else{

            foreach ($nbRapportDataMoisTotal as $n){
            ?>
            <td><?php echo number_format($n->res,2,'.',' ').'%'; ?></td>
            <?php
            }
            }
            ?>
            <?php
            $nbRapportConsultMoisTotal = \Illuminate\Support\Facades\DB::select('SELECT SUM(t.rapportConsMois) as "res"
                                                                                 FROM (SELECT COUNT(b.usage_name) as "rapportConsMois"
                                                                                    FROM billing_stats b, subscribers s
                                                                                    WHERE b.subscriber_name LIKE "%'.$pays.'"
                                                                                    AND lower(s.sector) = "autre sfd"
                                                                                    AND b.subscriber_name=s.name
                                                                                    AND ((lower(b.usage_name) = "rapport de crédit bic civ plus" AND lower(b.detail_name)="données sur le crédit") OR lower(b.usage_name) = "rapport de crédit bic civ vide")
                                                                                    AND b.stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                    GROUP BY b.subscriber_name) t');
            if (empty($nbRapportConsultMoisTotal)){
            ?>
            <td>0</td>
            <?php
            }
            else{
            foreach ($nbRapportConsultMoisTotal as $n){
            ?>
            <td><?php echo $n->res; ?></td>
            <?php
            }
            }
            ?>
            <td>200</td>
        </tr>
        <tr>
            <td></td>
            <td>VARIANCE P/P-1</td>
        </tr>
    </tbody>
</table>
<?php
    }
?>
