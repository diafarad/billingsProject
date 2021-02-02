@extends('layout')

@section('content')

<style>
  .push-top {
    margin-top: 50px;
  }
</style>

    <span class="navbar-brand mb-0 h1"><a class="btn" style="color: white" href="/">Accueil</a></span>

<div class="push-top">
    <center>
    <h2>Facturation project</h2>
    </center>
  @if(session()->get('success'))
    <div class="alert alert-success">
      {{ session()->get('success') }}
    </div><br />
  @endif
    <select class="form-select" aria-label="Default select example" name="pays" id="pays">
        <option value="">Sélectionner le pays</option>
        <option value="BF">BF - Burkina Faso</option>
        <option value="BJ">BJ - Bénin</option>
        <option value="CI">CI - Cote d'Ivoire</option>
        <option value="GW">GW - Guinée Bissau</option>
        <option value="ML">ML -Mali</option>
        <option value="NE">NE - Niger</option>
        <option value="TG">TG- Togo</option>
        <option value="SN">SN - Sénégal</option>
    </select>
    <select class="form-select" aria-label="Default select example" name="mois" id="mois">
        <option value="">Choisir le Mois</option>
        <option value="01">01 - Janvier</option>
        <option value="02">02 - Février</option>
        <option value="03">03 - Mars</option>
        <option value="04">04 - Avril</option>
        <option value="05">05 - Mai</option>
        <option value="06">06 - Juin</option>
        <option value="07">07 - Juillet</option>
        <option value="08">08 - Aôut</option>
        <option value="09">09 - Septembre</option>
        <option value="10">10 - Octobre</option>
        <option value="11">11 - Novembre</option>
        <option value="12">12 - Décembre</option>
    </select>
    <a style="float: right; margin-left: 5px" class="btn btn-primary" href="/myexport">Export</a>
    <a style="float: right" class="btn btn-info" href="/importExportView">View</a>
  <table id="resData" class="table">
    <thead>
        <tr class="table-info">
          <!--<td>ID</td>-->
          <td>Assujeti</td>
          <td>Rapport vide</td>
          <td>Raport avec données</td>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $rep)
        <tr>
            <td>{{$rep->name}}</td>
            <td>{{$rep->RapportVide}}</td>
            <td>{{$rep->RapportData}}</td>
        </tr>
        @endforeach
    </tbody>
  </table>
<div>
@endsection
