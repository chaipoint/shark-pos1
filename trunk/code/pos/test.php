
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/bootstrapValidator.css">


<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrapValidator.js"></script>
<script src="js/language/en_US.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('#html5Form').bootstrapValidator();
	
});

</script>
</head>
<body><form id="html5Form" method="post" class="form-horizontal"
      data-bv-message="This value is not valid"
      data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
      data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
      data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
    <div class="form-group">
        <label class="col-lg-3 control-label">Username</label>
        <div class="col-lg-5">
            <input type="text" class="form-control" name="username"
                data-bv-message="The username is not valid"

                required
                data-bv-notempty-message="The username is required and cannot be empty"

                pattern="^[a-zA-Z0-9]+$"
                data-bv-regexp-message="The username can only consist of alphabetical, number" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label">Email address</label>
        <div class="col-lg-5">
            <input class="form-control" name="email"
                required
                type="email" data-bv-emailaddress-message="The input is not a valid email address" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label">Website</label>
        <div class="col-lg-5">
            <input class="form-control" name="website"
                required
                type="url" data-bv-uri-message="The input is not a valid website address" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label">Fav color</label>
        <div class="col-lg-3">
            <input class="form-control" name="color"
                required
                type="color" data-bv-hexcolor-message="The input is not a valid color code" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label">Age</label>
        <div class="col-lg-2">
            <input type="text" class="form-control" name="age"
                required
                min="10"
                data-bv-greaterthan-inclusive="true"
                data-bv-greaterthan-message="The input must be greater than or equal to 10"

                max="100"
                data-bv-lessthan-inclusive="false"
                data-bv-lessthan-message="The input must be less than 100" />
        </div>
    </div>
</form>
</body>
</html>