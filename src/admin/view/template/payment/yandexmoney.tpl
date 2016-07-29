<?php echo $header; ?>
	<link rel="stylesheet" href="https://yastatic.net/bootstrap/3.3.6/css/bootstrap.min.css">
	<div id='content' class="container">
		<div class='row'>
			<h3 class="form-heading"><?php echo $lang_setting_head; ?></h3>
			<div class='col-md-12'>
				<p><?php echo $lang_license; ?></p>
				<p><?php echo $lang_version; ?> <span id='ya_version'><?php echo $yandexmoney_version; ?></span></p>
			</div>
		</div>
		<div class='row'>
			<div class='col-md-12'>
				<?php if (isset($attention)) { ?>
					<div class="attention"><?php echo $attention; ?></div>
				<?php } ?>
			</div>
			<div class='col-md-12'>
				<?php if (isset($success)) { ?>
					<div class="success"><?php echo $success; ?></div>
				<?php } ?>
			</div>
			<div class='col-md-12'>
				<?php if (isset($errors)) {
					foreach ($errors as $error){?>
						<div class="warning"><?php echo $error; ?></div>
				<?php }} ?>
			</div>
		</div>
		<form action="<?php echo $action; ?>" method="post" id="form">
		<!-- Навигация -->
		<ul class="nav nav-tabs" role="tablist">
			<li id='tabKassa' class="<?php if($ya_moneymode!='1'){echo 'active';} ?>"><a href="#kassa" class="my-tabs" aria-controls="kassa" role="tab" data-toggle="tab"><?php echo $lang_tab_kassa; ?></a></li>
			<li id='tabMoney' class="<?php if($ya_moneymode=='1'){echo 'active';} ?>"><a href="#money" class="my-tabs" aria-controls="money" role="tab" data-toggle="tab"><?php echo $lang_tab_money; ?></a></li>
			<!--<li><a href="#util" aria-controls="util" role="tab" data-toggle="tab">Дополнительно</a></li>-->
			<div class="buttons text-right">
				<a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
				<a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</ul>
		<div class="tab-content">
			<!-- row -->
			<div role="tabpanel" class="tab-pane active" id="kassa">
				<div class='row'>
					<div class='col-md-12'>
						<p><?php echo $lang_forwork_kassa; ?></p>
						<div class='form-horizontal'>
							<div class="form-group">
								<label for="ya_kassamode" class="col-sm-3 control-label"></label>
								<div class="col-sm-9">
									<label class='checkbox'>
										<input type="checkbox" name="ya_kassamode" class="cls_ya_kassamode"
											value="1" <?php if ($ya_kassamode=='1') { echo "checked"; }?>> <?php echo $lang_kassa_enable; ?>
									</label>
								</div>
							</div>
						</div>
						<div class='form-horizontal'>
							<div class="form-group">
								<label for="ya_workmode" class="col-sm-3 control-label"></label>
								<div class="col-sm-9">
									<label class='radio-inline'>
										<input type="radio" name="ya_workmode" class="cls_ya_workmode" value="0" <?php if ($ya_workmode =='0') { echo "checked"; }?>><?php echo $lang_testmode; ?>
									</label>
									<label class='radio-inline'>
										<input type="radio" name="ya_workmode" class="cls_ya_workmode" value="1" <?php if ($ya_workmode !='0') { echo "checked"; }?>><?php echo $lang_workmode; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">checkUrl/avisoUrl</label>
								<div class='col-sm-8'>
									<input class='form-control disabled' value='<?php echo $callback_url; ?>' disabled>
									<p class="help-block"><?php echo $lang_checkUrl_help; ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">successUrl/failUrl</label>
								<div class='col-sm-8'>
									<input class='form-control disabled' value='<?php echo $lang_successUrl; ?>' disabled>
									<p class="help-block"><?php echo $lang_successUrl_help; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row -->
				<div class='row'>
					<h4 class="form-heading"><?php echo $lang_lk_kassa; ?></h4>
					<div class='col-md-12'>
						<div class="form-horizontal">
							<div class="form-group">
								<label for="ya_shopid" class="col-sm-3 control-label">Shop ID</label>
								<div class="col-sm-9">
									<input name='ya_shopid' type="text" class="form-control" id="ya_shopid" value="<?php echo $ya_shopid; ?>">
									<p class="help-block"><?php echo $lang_shopid; ?></p>
								</div>
							</div>
							<div class="form-group">
								<label for="inputPassword3" class="col-sm-3 control-label">scid</label>
								<div class="col-sm-9">
									<input name='ya_scid' type="text" class="form-control" id="ya_scid" value="<?php echo $ya_scid; ?>">
									<p class="help-block"><?php echo $lang_scid; ?></p>
								</div>
							</div>
							<div class="form-group">
								<label for="inputPassword3" class="col-sm-3 control-label">ShopPassword</label>
								<div class="col-sm-9">
									<input name='ya_shopPassword' type="text" class="form-control" id="ya_shopPassword"  value="<?php echo $ya_shopPassword; ?>">
									<p class="help-block"><?php echo $lang_shopPassword; ?></p>
								</div>
								<p class='col-sm-9 col-sm-offset-3'><?php echo $lang_lk_help; ?></p>
							</div>
						</div>
					</div>
				</div>
				<!-- row -->
				<div class='row'>
					<h4 class="form-heading"><?php echo $lang_paymode_head; ?></h4>
					<div class='col-md-12'>
						<div class='form-horizontal'>
							<div class="form-group">
								<label for="ya_paymode" class="col-sm-3 control-label"><?php echo $lang_paymode_label; ?></label>
								<div class="col-sm-9">
									<input type="radio" name="ya_paymode" class="cls_ya_paymode" onclick='onChangePayMode()' value='kassa' <?php if ($ya_paymode =='kassa') { echo "checked"; }?>> <?php echo $lang_smartpay; ?>
								</div>
								<div class="col-sm-9 col-sm-offset-3">
									<input type="radio" name="ya_paymode" class="cls_ya_paymode" onclick='onChangePayMode()' value='shop' <?php if ($ya_paymode!='kassa') { echo "checked"; }?>> <?php echo $lang_shoppay; ?>
									<p class="help-block"><?php echo $lang_paymode_help; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class='row selectPayOpt'>
					<div class='col-md-12'>
						<div class='form-horizontal' role="form">
							<div class="form-group">
								<div class="col-sm-9 col-sm-offset-3">
									<p><?php echo $lang_option_help; ?></p>
									<?php foreach ($name_methods as $val => $name){ ?>
									<div class="checkbox">
										<label><input name='ya_paymentOpt[]' class="cls_ya_paymentOpt" type="checkbox" value="<?php echo $val;?>" <?php if (is_array($ya_paymentOpt) && in_array($val, $ya_paymentOpt)) { echo "checked"; }?>> <?php echo $name;?> </label>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- для tab-kassa -->
			<div role="tabpanel" class="tab-pane" id="money">
				<div class='row'>
					<div class='col-md-12'>
						<p><?php echo $lang_forwork_money; ?></p>
						<div class='form-horizontal'>
							<div class="form-group">
								<label for="ya_moneymode" class="col-sm-3 control-label"></label>
								<div class="col-sm-9">
									<label class='checkbox'>
										<input type="checkbox" name="ya_moneymode" class="cls_ya_moneymode" value="1"
											<?php if ($ya_moneymode=='1') { echo "checked"; }?>> <?php echo $lang_enable_money; ?>
									</label>
								</div>
							</div>
						</div>
						<div class='form-horizontal'>
							<div class="form-group">
								<label class="control-label col-sm-3">RedirectURL</label>
								<div class='col-sm-8'>
									<input class='form-control disabled' value='<?php echo $callback_url; ?>' disabled>
								</div>
							</div>
							<div class="form-group">
								<div class='col-sm-8 col-sm-offset-3'>
									<?php echo $lang_redirectUrl_help; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row -->
				<div class='row'>
					<h4 class="form-heading"><?php echo $lang_account_head; ?></h4>
					<div class='col-md-12'>
						<div class='form-horizontal'>
							<div class="form-group">
								<label for="ya_wallet" class="col-sm-3 control-label"><?php echo $lang_wallet; ?></label>
								<div class="col-sm-9">
									<input name='ya_wallet' type="text" class="form-control" id="ya_wallet" value="<?php echo $ya_wallet; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="ya_appPassword" class="col-sm-3 control-label"><?php echo $lang_password; ?></label>
								<div class="col-sm-9">
									<input name='ya_appPassword' type="text" class="form-control" id="ya_appPassword" value="<?php echo $ya_appPassword; ?>">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9 col-sm-offset-3">
									<?php echo $lang_account_help; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- для tab-money -->
			<!-- row -->
			<div class='row clsOnlyMoney'>
				<div class='col-md-12'>
					<div class='form-horizontal' role="form">
						<div class="form-group">
							<label for="ya_paymentOpt_wallet" class="col-sm-3 control-label"><?php echo $lang_option_wallet; ?></label>
							<div class="col-sm-9">
								<?php foreach (array('PC' => $name_methods['PC'], 'AC' => $name_methods['AC']) as $val => $name){ ?>
									<div class="checkbox">
										<label><input name='ya_paymentOpt_wallet[]' type="checkbox" value="<?php echo $val;?>" <?php if (is_array($ya_paymentOpt_wallet) && in_array($val, $ya_paymentOpt_wallet)) { echo "checked"; }?>> <?php echo $name;?> </label>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='row selectPayOpt clsOnlyKassa'>
				<div class='col-md-12'>
					<p><a href='' target='_blank'> </a></p>
					<div class='form-horizontal'>
						<div class="form-group">
							<label class="control-label col-sm-3"><?php echo $lang_optDefault; ?></label>
							<div class='col-sm-8'>
								<select name='ya_paymentDfl' class="form-control">
									<?php foreach ($name_methods as $val => $name){
										$checked = ($ya_paymentDfl == $val)?'selected="selected"':'';?>
										<option value='<?php echo $val; ?>' <?php echo $checked; ?>>
										<?php echo $name; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- row -->
			<div class='row'>
				<div class='col-md-12'>
					<p><a href='' target='_blank'> </a></p>
					<div class='form-horizontal'>
						<div class="form-group">
							<label class="control-label col-sm-3"><?php echo $lang_successPage_label; ?></label>
							<div class='col-sm-8'>
								<select name='ya_pageSuccess' class="form-control">
									<option value='0' <?php if ($ya_pageSuccess =='0') echo 'selected="selected"'; ?>>---<?php echo $lang_page_standart; ?></option>
									<?php foreach ($pages_mpos as $page_success) {
										$mp_checked = ($page_success['information_id'] == $ya_pageSuccess)?'selected="selected"':''; ?>
										<option value="<?php echo $page_success['information_id']; ?>" <?php echo $mp_checked;?>>
										<?php echo $page_success['title']; ?></option>
									<?php } ?>
								</select>
								<p class='help-block'><?php echo $lang_successPage_help; ?></p>
							</div>
						</div>
						<div class="form-group clsOnlyKassa">
							<label class="control-label col-sm-3"><?php echo $lang_failPage_label; ?></label>
							<div class='col-sm-8'>
								<select name='ya_pageFail' class="form-control">
									<option value='0' <?php if ($ya_pageFail =='0') echo 'selected="selected"'; ?>>---<?php echo $lang_page_standart; ?></option>
									<?php foreach ($pages_mpos as $page) {
										$mp_checked = ($page['information_id'] == $ya_pageFail)?'selected="selected"':''; ?>
										<option value="<?php echo $page['information_id']; ?>" <?php echo $mp_checked;?>>
											<?php echo $page['title']; ?></option>
									<?php } ?>
								</select>
								<p class='help-block'><?php echo $lang_failPage_help; ?></p>
							</div>
						</div>
						<div class="form-group clsOnlyKassa">
							<label class="control-label col-sm-3"><?php echo $lang_successMP_label; ?></label>
							<div class='col-sm-8'>
								<select name='ya_pageSuccessMP' class="form-control">
									<?php foreach ($pages_mpos as $page) {
										$mp_checked = ($page['information_id'] == $ya_pageSuccessMP)?'selected="selected"':''; ?>
										<option value="<?php echo $page['information_id']; ?>" <?php echo $mp_checked;?>>
											<?php echo $page['title']; ?></option>
									<?php } ?>
								</select>
								<p class="help-block"><?php echo $lang_successMP_help; ?></p>
							</div>
						</div>
						<div class="form-group clsOnlyKassa">
							<label class="control-label col-sm-3"><?php echo $lang_namePay_label; ?></label>
							<div class='col-sm-8'>
								<input name='ya_namePaySys' type="text" class="form-control" id="inputEmail3" value="<?php echo $ya_namePaySys; ?>" data-toggle="tooltip" data-placement="left">
								<p class="help-block"><?php echo $lang_namePay_help; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='row'>
				<h4 class="form-heading"><?php echo $lang_feature_head; ?></h4>
				<div class='col-md-12'>
					<p><a href='' target='_blank'> </a></p>
					<div class='form-horizontal'>
						<div class="form-group">
							<label for="ya_debugmode" class="col-sm-3 control-label"><?php echo $lang_debug_label; ?></label>
							<div class="col-sm-9">
								<label class='radio-inline'>
									<input type="radio" name="ya_debugmode" class="cls_ya_debugmode" value='0' <?php if ($ya_debugmode !='1') { echo "checked"; }?>> <?php echo $lang_off; ?>
								</label>
								<label class='radio-inline'>
									<input type="radio" name="ya_debugmode" class="cls_ya_debugmode" value='1' <?php if ($ya_debugmode =='1') { echo "checked"; }?>> <?php echo $lang_on; ?>
								</label>
								<p class='help-block'><?php echo $lang_debug_help; ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3"><?php echo $lang_newStutus_label; ?></label>
							<div class='col-sm-8'>
								<select name='ya_newStatus' class="form-control" data-toggle="tooltip" data-placement="left" title="">
									<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $ya_newStatus) { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3"><?php echo $lang_sordOrder_label; ?></label>
							<div class='col-sm-8'>
								<input name='ya_sortOrder' type="text" class="form-control" id="ya_sortOrder" value="<?php echo (int) $ya_sortOrder; ?>" data-toggle="tooltip" data-placement="left">
								<p class="help-block"></p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3"><?php echo $lang_idZone_label; ?></label>
							<div class='col-sm-8'>
								<select name='ya_idZone' class="form-control" data-toggle="tooltip" data-placement="left" title="">
									<option value="0"><?php echo $text_all_zones; ?></option>
									<?php foreach ($geo_zones as $geo_zone) { ?>
										<?php if ($geo_zone['geo_zone_id'] == $ya_idZone) { ?>
											<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- end row -->
		</div> <!-- для tab-контента -->
		</form>
	</div> <!-- есть в footer -->
<script>
	function onChangePayMode(){
		if ($("input[name=ya_paymode]:checked").val()=='kassa'){
			$(".selectPayOpt").hide();
		}else{
			$(".selectPayOpt").show();
		}
	}
	function onChoiseService(){
		if ($("#tabMoney").hasClass("active")){
			$(".clsOnlyMoney").show();
			$(".clsOnlyKassa").hide();
		}else{
			$(".clsOnlyKassa").show();
			$(".clsOnlyMoney").hide();
			onChangePayMode();
		}
	}
	$( document ).ready(function( $ ) {
		var arMethods = [];
		var selDefault = '';
		$('.cls_ya_paymentOpt').click(function () {
			var chkPaymentOpt = $('input.cls_ya_paymentOpt:checked');
			arMethods = chkPaymentOpt.map(function(){
				return  $(this).val();
			}).toArray();
			selDefault = '';
			$("select[name=ya_paymentDfl] > option").each(function() {
				if (arMethods.indexOf($(this).val())==-1) {
					$(this).hide();
				}else{
					$(this).show();
					selDefault = $(this).val();
				}
			});
			if (selDefault == ''){
				$("select[name=ya_paymentDfl]").parent('div').parent('div').hide();
			}else {
				$("select[name=ya_paymentDfl]").parent('div').parent('div').show();
				$("select[name=ya_paymentDfl] option[value='" + selDefault + "']").attr("selected", "selected");
			}
		});
		$('.my-tabs').click(function (e) {
			e.preventDefault();
			if ($(this).attr("href")=="#money"){
				$("#tabMoney").addClass("active");
				$("#tabKassa").removeClass("active");
				$("#money").show();
				$("#kassa").hide();
			}else{
				$("#tabMoney").removeClass("active");
				$("#tabKassa").addClass("active");
				$("#money").hide();
				$("#kassa").show();
			}
			onChoiseService();
		});
		$("li.active > a.my-tabs").trigger( "click" );
		onChangePayMode();
		onChoiseService();
	});
</script>
<?php echo $footer; ?>