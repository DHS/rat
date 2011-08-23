
<ul class="tabs">
  <li class="active"><a href="#">Profile</a></li>
  <li><a href="#">Password</a></li>
</ul>

<form action="/settings/password" method="post">
  <fieldset>
    <legend>Change password</legend>
    <div class="clearfix">
      <label for="">Old password</label>
      <div class="input">
        <input class="medium" name="old_password" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="clearfix">
      <label for="">New password</label>
      <div class="input">
        <input class="medium" name="new_password1" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="clearfix">
      <label for="">New password again</label>
      <div class="input">
        <input class="medium" name="new_password2" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="actions">
      <button type="submit" class="btn primary">Save</button>
    </div>
  </fieldset>
</form>

<form action="/settings/profile" method="post">
  <fieldset>
    <legend>Update profile</legend>
    <div class="clearfix">
      <label for="">Full name</label>
      <div class="input">
        <input class="medium" name="name" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="clearfix">
      <label for="">Bio</label>
      <div class="input">
        <input class="medium" name="bio" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="clearfix">
      <label for="">URL</label>
      <div class="input">
        <input class="medium" name="url" size="30" type="text" />
      </div>
    </div> <!-- /clearfix -->
    <div class="actions">
      <button type="submit" class="btn primary">Save</button>
    </div>
  </fieldset>
</form>
