<table class="table">
    <thead>
    <tr>
        <th></th>
        <th>Jour</th>
        <th>
            <?php echo date('d/m/Y')?>
        </th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th colspan="5">Résumé des consultations journalières (Banques et Etablissements Financiers)</th>
        <th colspan="6">Résumé des consultations mensuelles (Banques et Etablissements Financiers)</th>
    </tr>
    <tr>
        <th></th>
        <th>Inst.</th>
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
    @foreach($institutions as $inst)
        <tr>
            <td></td>
            <td>{{$inst->name}}</td>
            <td>0</td>
            <?php
            ini_set('max_execution_time', 500);
            /*$nbUserAvailable = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userAv"
                                                                       FROM billing_stats
                                                                       WHERE subscriber_name = "'.$inst->name.'"
                                                                       GROUP BY subscriber_name');*/
            $nbUserConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userCons"
                                                                     FROM billing_stats
                                                                     WHERE subscriber_name = "'.$inst->name.'"
                                                                     AND stats_date = "2020-12-31"
                                                                     GROUP BY subscriber_name');
            $nbRapportConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportCons"
                                                                        FROM billing_stats
                                                                        WHERE subscriber_name = "'.$inst->name.'"
                                                                        AND (lower(usage_name) = "rapport de crédit bic civ plus" OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                        AND stats_date = "2020-12-31"
                                                                        GROUP BY subscriber_name');
            $prcentRapportData = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "Pourcentage"
                                                                         FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                               FROM billing_stats
                                                                               WHERE subscriber_name = "'.$inst->name.'"
                                                                               AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                               AND stats_date = "2020-12-31"
                                                                               GROUP BY subscriber_name) b1,
                                                                              (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                               FROM billing_stats
                                                                               WHERE subscriber_name = "'.$inst->name.'"
                                                                               AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                               AND stats_date = "2020-12-31"
                                                                               GROUP BY subscriber_name) b2
                                                                         WHERE b1.subscriber_name=b2.subscriber_name');
            $nbUserMoisConsult = \Illuminate\Support\Facades\DB::select('SELECT COUNT(DISTINCT(user_name)) as "userMoisCons"
                                                                         FROM billing_stats
                                                                         WHERE subscriber_name = "'.$inst->name.'"
                                                                         AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                         GROUP BY subscriber_name');
            $volumeMoyen = \Illuminate\Support\Facades\DB::select('SELECT (COUNT(usage_name)/20) as "volMoy"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$inst->name.'"
                                                                            AND (lower(usage_name) = "rapport de crédit bic civ plus" OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                            GROUP BY subscriber_name');
            $prcentRapportDataMois = \Illuminate\Support\Facades\DB::select('SELECT ((b1.nbData*100)/(b1.nbData+b2.nbVide)) as "PourcentageMois"
                                                                             FROM (SELECT subscriber_name, COUNT(usage_name) as "nbData"
                                                                                   FROM billing_stats
                                                                                   WHERE subscriber_name = "'.$inst->name.'"
                                                                                   AND lower(usage_name) = "rapport de crédit bic civ plus" AND lower(detail_name) = "données sur le crédit"
                                                                                   AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                   GROUP BY subscriber_name) b1,
                                                                                  (SELECT subscriber_name, COUNT(usage_name) as "nbVide"
                                                                                   FROM billing_stats
                                                                                   WHERE subscriber_name = "'.$inst->name.'"
                                                                                   AND lower(usage_name) = "rapport de crédit bic civ vide"
                                                                                   AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                                   GROUP BY subscriber_name) b2
                                                                             WHERE b1.subscriber_name=b2.subscriber_name');
            $nbRapportConsultMois = \Illuminate\Support\Facades\DB::select('SELECT COUNT(usage_name) as "rapportConsMois"
                                                                            FROM billing_stats
                                                                            WHERE subscriber_name = "'.$inst->name.'"
                                                                            AND (lower(usage_name) = "rapport de crédit bic civ plus" OR lower(usage_name) = "rapport de crédit bic civ vide")
                                                                            AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                                            GROUP BY subscriber_name');
            /*if(empty($nbUserAvailable)){
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
            }*/
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
        </tr>
    @endforeach
    </tbody>
</table>
