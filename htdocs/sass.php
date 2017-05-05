<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="web/css/style.css">
</head>
<body>
<div class="container">

    <h1>Bootstrap grid examples</h1>
    <p class="alert-danger">First: execute the following Symfony Command bin/console cache:web:create</p>
    <p class="lead">Basic grid layouts to get you familiar with building within the Bootstrap grid system.</p>

    <h3>Five grid tiers</h3>
    <p>There are five tiers to the Bootstrap grid system, one for each range of devices we support. Each tier starts at
        a minimum viewport size and automatically applies to the larger devices unless overridden.</p>

    <div class="row">
        <div class="col-4">.col-4</div>
        <div class="col-4">.col-4</div>
        <div class="col-4">.col-4</div>
    </div>

    <div class="row">
        <div class="col-sm-4">.col-sm-4</div>
        <div class="col-sm-4">.col-sm-4</div>
        <div class="col-sm-4">.col-sm-4</div>
    </div>

    <div class="row">
        <div class="col-md-4">.col-md-4</div>
        <div class="col-md-4">.col-md-4</div>
        <div class="col-md-4">.col-md-4</div>
    </div>

    <div class="row">
        <div class="col-lg-4">.col-lg-4</div>
        <div class="col-lg-4">.col-lg-4</div>
        <div class="col-lg-4">.col-lg-4</div>
    </div>

    <div class="row">
        <div class="col-xl-4">.col-xl-4</div>
        <div class="col-xl-4">.col-xl-4</div>
        <div class="col-xl-4">.col-xl-4</div>
    </div>

    <h3>Three equal columns</h3>
    <p>Get three equal-width columns <strong>starting at desktops and scaling to large desktops</strong>. On mobile
        devices, tablets and below, the columns will automatically stack.</p>
    <div class="row">
        <div class="col-md-4">.col-md-4</div>
        <div class="col-md-4">.col-md-4</div>
        <div class="col-md-4">.col-md-4</div>
    </div>

    <h3>Three unequal columns</h3>
    <p>Get three columns <strong>starting at desktops and scaling to large desktops</strong> of various widths.
        Remember, grid columns should add up to twelve for a single horizontal block. More than that, and columns start
        stacking no matter the viewport.</p>
    <div class="row">
        <div class="col-md-3">.col-md-3</div>
        <div class="col-md-6">.col-md-6</div>
        <div class="col-md-3">.col-md-3</div>
    </div>

    <h3>Two columns</h3>
    <p>Get two columns <strong>starting at desktops and scaling to large desktops</strong>.</p>
    <div class="row">
        <div class="col-md-8">.col-md-8</div>
        <div class="col-md-4">.col-md-4</div>
    </div>

    <h3>Full width, single column</h3>
    <p class="text-warning">No grid classes are necessary for full-width elements.</p>

    <hr>

    <h3>Two columns with two nested columns</h3>
    <p>Per the documentation, nesting is easy—just put a row of columns within an existing column. This gives you two
        columns <strong>starting at desktops and scaling to large desktops</strong>, with another two (equal widths)
        within the larger column.</p>
    <p>At mobile device sizes, tablets and down, these columns and their nested columns will stack.</p>
    <div class="row">
        <div class="col-md-8">
            .col-md-8
            <div class="row">
                <div class="col-md-6">.col-md-6</div>
                <div class="col-md-6">.col-md-6</div>
            </div>
        </div>
        <div class="col-md-4">.col-md-4</div>
    </div>

    <hr>

    <h3>Mixed: mobile and desktop</h3>
    <p>The Bootstrap v4 grid system has five tiers of classes: xs (extra small), sm (small), md (medium), lg (large),
        and xl (extra large). You can use nearly any combination of these classes to create more dynamic and flexible
        layouts.</p>
    <p>Each tier of classes scales up, meaning if you plan on setting the same widths for xs and sm, you only need to
        specify xs.</p>
    <div class="row">
        <div class="col-12 col-md-8">.col-12 .col-md-8</div>
        <div class="col-6 col-md-4">.col-6 .col-md-4</div>
    </div>
    <div class="row">
        <div class="col-6 col-md-4">.col-6 .col-md-4</div>
        <div class="col-6 col-md-4">.col-6 .col-md-4</div>
        <div class="col-6 col-md-4">.col-6 .col-md-4</div>
    </div>
    <div class="row">
        <div class="col-6">.col-6</div>
        <div class="col-6">.col-6</div>
    </div>

    <hr>

    <h3>Mixed: mobile, tablet, and desktop</h3>
    <p></p>
    <div class="row">
        <div class="col-12 col-sm-6 col-lg-8">.col-12 .col-sm-6 .col-lg-8</div>
        <div class="col-6 col-lg-4">.col-6 .col-lg-4</div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-4">.col-6 .col-sm-4</div>
        <div class="col-6 col-sm-4">.col-6 .col-sm-4</div>
        <div class="col-6 col-sm-4">.col-6 .col-sm-4</div>
    </div>

    <hr>

    <h3>Column clearing</h3>
    <p><a href="../../layout/grid/#example-responsive-column-resets">Clear floats</a> at specific breakpoints to prevent
        awkward wrapping with uneven content.</p>
    <div class="row">
        <div class="col-6 col-sm-3">
            .col-6 .col-sm-3
            <br>
            Resize your viewport or check it out on your phone for an example.
        </div>
        <div class="col-6 col-sm-3">.col-6 .col-sm-3</div>

        <!-- Add the extra clearfix for only the required viewport -->
        <div class="clearfix hidden-sm-up"></div>

        <div class="col-6 col-sm-3">.col-6 .col-sm-3</div>
        <div class="col-6 col-sm-3">.col-6 .col-sm-3</div>
    </div>

    <hr>

    <h3>Offset, push, and pull resets</h3>
    <p>Reset offsets, pushes, and pulls at specific breakpoints.</p>
    <div class="row">
        <div class="col-sm-5 col-md-6">.col-sm-5 .col-md-6</div>
        <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">.col-sm-5 .offset-sm-2 .col-md-6 .offset-md-0</div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-5 col-lg-6">.col-sm-6 .col-md-5 .col-lg-6</div>
        <div class="col-sm-6 col-md-5 offset-md-2 col-lg-6 offset-lg-0">.col-sm-6 .col-md-5 .offset-md-2 .col-lg-6
            .offset-lg-0
        </div>
    </div>

</div> <!-- /container -->

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"
        integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
        integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
        crossorigin="anonymous"></script>
</body>
</html
