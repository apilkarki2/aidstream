<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <title>Aidstream</title>
    <link rel="shortcut icon" type="image/png" sizes="16*16" href="images/favicon.png"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.min.css">
</head>
<body>
@include('includes.header')
<section class="main-container">
    <div class="organisation-list-wrapper">
        <div class="col-md-12 text-center">
            @include('includes.response')
            <h1>{{ count($organizations) }} organisations are using AidStream</h1>
            <p>The organisations listed below are using AidStream.</p>
            <div class="search-org">
                <label for="search">Search:</label>
                <input id="search" type="text">
            </div>
            <div class="organisations-list width-900">
                <ul class="org_list">
                    @foreach($organizations as $index => $organization)
                        <li>
                        <a href="{{ url('/who-is-using/'.$organization->org_slug)}}">
                            <label for="org_logo">{{ $organization->name }}</label>
                            <img id="org_logo" src="{{ $organization->logo_url }}" alt="{{ $organization->name }}">
                        </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
@include('includes.footer')
{{--<div class="hidden">--}}
{{--<ul class="no-image-logo">--}}
{{--<li><span><a href=""></a></span></li>--}}
{{--</ul>--}}
{{--<ul class="has-image-logo">--}}
{{--<li><a href=""><img/></a></li>--}}
{{--</ul>--}}
{{--</div>--}}
<script type="text/javascript" src="{{url('/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('/js/bootstrap.min.js')}}"></script>
<script>
    $("#search").on("keyup", function () {
        var g = $(this).val().toLowerCase();
        $(".org_list li a label").each(function () {
            var s = $(this).text().toLowerCase();
            $(this).closest('.org_list li')[s.indexOf(g) !== -1 ? 'show' : 'hide']();
        });
    });
</script>
</body>
</html>
