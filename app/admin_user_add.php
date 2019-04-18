<link rel="stylesheet" type="text/css" href="./dist/components/table.min.css">

<div class="ui main container">

<!-- DATA FORM -->
<h4 class="ui top attached header compact">
    User Form
    <!-- <i class='close mini icon' onclick="$('#user_form1').transition('fade');"></i> -->
</h4>
    <div class="ui blue attached segment compact" id="user_form1">
    <form name="user-form" id="user-form" class="ui form">
    <div class="ui negative message transition hidden" id="error_message"></div>
        <div class="two fields">
            <div class="field">
                <label>User Name</label>
                <input type="text" name="user_name" placeholder="User Name">
            </div>
            <div class="field">
                <label>Email</label>
                <input type="text" name="user_email" placeholder="User Email">
            </div>
        </div>

      <?php
        if(!isset($edit_id)) {
       ?>

        <div class="two fields">
            <div class="field">
                <label>Password</label>
                <input type="password" name="user_password" placeholder="Password">
            </div>
            <div class="field">
                <label>Confirm Password</label>
                <input type="password" name="confirm-password" placeholder="Confirm Password">
            </div>
        </div>
      <?php
        }
       ?>

        <div class="two fields">
            <div class="field">
                <label>User Type</label>
                <div class="ui four column wide selection dropdown">
                    <input type="hidden" name="user_type" id="user_type">
                    <i class="dropdown icon"></i>
                    <div class="default text">User Type</div>
                    <div class="menu">
                        <div class="item" data-value="1">Super Admin</div>
                        <div class="item" data-value="2">Admin</div>
                        <div class="item" data-value="3">Faculty</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="field">
            <div class="ui checkbox">
            <input type="checkbox" tabindex="0" class="hidden">
            <label>I agree to the Terms and Conditions</label>
            </div>
        </div>
        <button class="ui button" type="submit" id="user-form-submit">Submit</button>
        <div class="ui error message"></div>
    </form>
    </div>
    <h4 class="ui dividing header">Recent</h4>
    <div id="table1_datawindow" class="table_datawindow"></div>
    <!-- <div class="content" id="info"></div> -->
    <div id="table1_pagination" class="eleven wide column"></div>
    <div class="five wide column right floated right aligned">
        <h4 class="ui right floated">
            <div class="content" id="table1_info"></div>
        </h4>
    </div>
</div>

<?php require "./app/footer.php"; ?>

<script src="javascript/jquery-3.3.1.min.js"></script>
<script src="dist/components/form.min.js"></script>
<script src="dist/components/transition.min.js"></script>
<script src="dist/components/dropdown.min.js"></script>
<script src="dist/components/checkbox.min.js"></script>
<script src="javascript/pagination.js"></script>
<script src="javascript/tabulation.js"></script>
<script>
  var table1= new Tabulation({ 
          apiUrl:"<?=$app_root?>/api/?function=user_list&option=recent&pgno=",
          addUrl:"<?=$app_root?>/api/?function=user_add",
          delUrl:"<?=$app_root?>/api/?function=user_delete&option=recent&del_id=", 
          editUrl:"<?=$app_root?>/api/?function=user_edit&row_id=",
          fetchUrl:"<?=$app_root?>/api/?function=user_fetch&row_id=",
          paging:false
        });

  function sendData(e) {
      var close_button_text="<i class='close icon' onclick=\"$('#error_message').transition('fade');\"></i>";

      $('#error_message').html('');
      $("#error_message").removeClass("visible").addClass("hidden");
      if( $('.ui.form').form('is valid')) {
            // form is valid (both email and name)
          $.post( "<?=isset($edit_id)?$app_root."/api/?function=user_edit&row_id=".$edit_id : $app_root."/api/?function=user_add"; ?>", 
          $( "#user-form" ).serialize()).done( 
          function (response) {
            var myObj = JSON.parse(response);
            if(myObj.status === "success") {
                // do something with response.message or whatever other data on success
                //console.log("Success:"+myObj.message);
                $('#user-form').form('reset');
                table1.loadPage(1, false);
                
            } else if(myObj.status === "error") {
                // do something with response.message or whatever other data on error
                $("#error_message").removeClass("hidden").addClass("visible");
                $('#error_message').html(close_button_text+myObj.message);
                //console.log("Error:"+myObj.message);
            }
        }).fail(function(){
            $("#error_message").removeClass("hidden").addClass("visible");
            $('#error_message').html(close_button_text+'Record could not be added.');
        });

      }
      e.preventDefault();
  }

  $(function() {
      //table1.init();
      table1.loadPage(1, true);
      $('.selection.dropdown').dropdown();
      $('.ui.checkbox').checkbox();
      <?php
        if(isset($edit_id)) {
       ?>
       $.get( "<?=$app_root."/api/?function=user_fetch&row_id=".$edit_id; ?>").done( 
          function (response) {
            var myObj = JSON.parse(response);
            var key_names;
            if(myObj.status === "success") {
                // do something with response.message or whatever other data on success
                $('#user-form').form('set values', myObj);
                
            } else if(myObj.status === "error") {
                $("#error_message").removeClass("hidden").addClass("visible");
                $('#error_message').html(close_button_text+myObj.message);
            }
        }).fail(function(){
            $("#error_message").removeClass("hidden").addClass("visible");
            $('#error_message').html(close_button_text+'Record could not be fetched.');
        });
        <?php
         }
         ?>

      document.getElementById("user-form").addEventListener('submit', sendData);

      $('.ui.form').form({
        fields: {
            type: {
                identifier: 'user_type',
                rules: [
                {
                    type   : 'empty',
                    prompt : 'Please select a user type'
                }
                ]
            },
            username: {
                identifier: 'user_name',
                rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter a username'
                }
                ]
            },
            password: {
                identifier: 'user_password',
                rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter a password'
                },
                {
                    type   : 'minLength[6]',
                    prompt : 'Your password must be at least {ruleValue} characters'
                }
                ]
            },
            confirmpass: {
                identifier: 'confirm-password',
                rules: [
                    {
                        type   : 'match[user_password]',
                        prompt : 'Passwords do not match'
                    }
                ]
            },
            email: {
                identifier: 'user_email',
                rules: [
                {
                    type   : 'email',
                    prompt : 'You must enter a valid email'
                }
                ]
            }
            }
        });
  });
</script>
</body>  
</html>
