<div class="mail-box">
	<div class="row">
		<div class="col-md-4 col-md-offset-4 text-center">
			<div id="notifBar" class="alert-fixed">
				Sample alert
			</div>
		</div>
	</div>
	<aside class="sm-side">
		<div class="user-head">
			<div class="user-name">
				<h3><a href="#"><?= $_SESSION['login']['email'] ?></a></h3>
			</div>
			<a title="Log out" class="mail-dropdown pull-right" href="<?= base_url('logout'); ?>">
				<i class="fa fa-sign-out" aria-hidden="true"></i>
			</a>
		</div>
		<div class="inbox-body">
			<a href="#myModal" data-toggle="modal"  title="Compose"    class="btn btn-compose">
				Compose
			</a>
			<!-- Modal -->
			<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
							<h4 class="modal-title">Compose</h4>
						</div>
						<div class="modal-body">
							<form id="sendMsgData" role="form" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group">
									<label class="col-lg-2 control-label">To</label>
									<div class="col-lg-10">
										<input required type="text" placeholder="" id="to" name="to" class="form-control" pattern="([\s]*;?[\s]*[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})+">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-2 control-label">Cc / Bcc</label>
									<div class="col-lg-10">
										<input type="text" placeholder="" id="cc" name="cc" class="form-control pattern="([\s]*;?[\s]*[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})+"">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-2 control-label">Subject</label>
									<div class="col-lg-10">
										<input type="text" placeholder="" id="subject" name="subject" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-2 control-label">Message</label>
									<div class="col-lg-10">
										<textarea rows="10" cols="30" class="form-control" id="body" name="body"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-2 control-label">Attachments</label>
									<div class="col-lg-7">
										<div class="form-control attachment-box">

										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										  <span class="btn green fileinput-button">
											<i class="fa fa-plus fa fa-white"></i>
											<span>Attachment</span>
											<input id="fileUploader" type="file" name="files[]" multiple="">
										  </span>
										<span class="input-group-btn">
											<a id="clearUploads" class="btn btn-default">CLEAR</a>
										</span>
									</div>
									<div class="form-group">
										<div class="col-md-4 col-md-offset-4">
											<button class="form-control btn btn-send">Send</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</div>

		<div id="mailboxes">
			<!-- Dynamic content -->
		</div>

	</aside>
	<aside class="lg-side">
		<div class="inbox-head">
			<h3>Inbox</h3>
		</div>
		<div id="messageMain" class="inbox-body">
			<!-- dynamic content here -->
		</div>
	</aside>
</div>
