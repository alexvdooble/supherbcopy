<?php
/**
 * Registration popup
 *
 * @package WordPress
 */

$bg = get_field( 'reg_p_bg', 'option' );
?>
<div style="display:none;">
	<div id="registerPopup">
		<div class="regPopLeft" <?php echo $bg ? 'style="background-image:url(' . $bg . ')"' : ''; ?>>
			<div class="regPopLeftTitle">
				<?php the_field( 'reg_p_bg_title', 'option' ); ?>
			</div>
			<div class="regPopLeftText">
				<?php the_field( 'reg_p_bg_text', 'option' ); ?>
			</div>
		</div>
		<div class="regPopRightForm">
			<div class="regPopSteps active">
				<div class="regPopRightFormTitle">
					<?php the_field( 'reg_p_title', 'option' ); ?>
				</div>
				<div class="regPopRightFormSubTitle">
					<?php the_field( 'reg_p_subtitle', 'option' ); ?>
				</div>
				<div class="wpcf7FormWrap">

					<form action="" method="post" class="register-form wpcf7-form init">
						<?php get_template_part( 'partials/global/ajax-loader' ); ?>
						<p>
							<span class="wpcf7-form-control-wrap w50left">
								<input type="text" name="name" class="wpcf7-form-control required" placeholder="<?php esc_html_e( 'שם פרטי', 'supherb' ); ?>" />
							</span>
							<span class="wpcf7-form-control-wrap w50right">
								<input type="text" name="lname" class="wpcf7-form-control required" placeholder="<?php esc_html_e( 'שם משפחה', 'supherb' ); ?>" />
							</span>
							<span class="wpcf7-form-control-wrap">
								<input type="email" name="email" class="wpcf7-form-control required email" placeholder="<?php esc_html_e( 'כתובת דוא״ל', 'supherb' ); ?>" />
								<span class="wpcf7-form-control-help-text">
									<?php esc_html_e( 'ישמש כשם משתמש', 'supherb' ); ?>
								</span>
							</span>
							<span class="wpcf7-form-control-wrap">
								<input type="password" name="password" class="wpcf7-form-control passwordField required"
									minlength="7"
									placeholder="<?php esc_html_e( 'סיסמא', 'supherb' ); ?>" autocomplete="off" />
								<button type="button" class="showPasswordBtn hide"></button>
							</span>
							<span class="wpcf7-form-control-wrap">
								<input type="password" name="retype_password" class="wpcf7-form-control passwordField required"
									minlength="7"
									placeholder="<?php esc_html_e( 'אימות סיסמא', 'supherb' ); ?>" autocomplete="off" />
								<button type="button" class="showPasswordBtn hide"></button>
							</span>
							<span class="checkbox-wrap">
								<span class="wpcf7-form-control-wrap">
									<span class="wpcf7-form-control">
										<span class="wpcf7-list-item">
											<label>
												<input type="checkbox" name="acceptance-716" />
												<span class="wpcf7-list-item-label">
													<?php esc_html_e( 'אני מאשר/ת קבלת תוכן השיווקי', 'supherb' ); ?>
												</span>
											</label>
										</span>
									</span>
								</span>
							</span>
							<span class="checkbox-wrap">
								<span class="wpcf7-form-control-wrap">
									<span class="wpcf7-form-control">
										<span class="wpcf7-list-item">
											<label>
												<input type="checkbox" name="acceptance-726" class="required" />
												<span class="wpcf7-list-item-label">
													<?php esc_html_e( 'קראתי ואני מסכים', 'supherb' ); ?>
													 <a href="#">לתנאי השימוש</a> <a href="#">ולמדיניות הפרטיות</a>
												</span>
											</label>
										</span>
									</span>
								</span>
							</span>
							<button type="button" class="wpcf7-form-control wpcf7-submit js_nextRegPopStep">
								<?php esc_html_e( 'המשך', 'supherb' ); ?>
							</button>
						</p>
					</form>
					<div class="register-error"></div>
				</div>
				<ul class="regSteps">
					<li><button type="button" class="regStepsBtn active"></button></li>
					<li><button type="button" class="regStepsBtn"></button></li>
					<li><button type="button" class="regStepsBtn"></button></li>
				</ul>
			</div>
			<div class="regPopSteps">
				<div class="regPopRightFormTitle">
					<?php the_field( 'reg_p_step2_title', 'option' ); ?>
				</div>
				<div class="wpcf7FormWrap">
					<form action="" method="post" class="wpcf7-form init register-form register-step-2">
						<?php get_template_part( 'partials/global/ajax-loader' ); ?>

						<input type="hidden" name="user_id" value="">
						<p>
							<span class="wpcf7-form-control-wrap">
								<input type="text" name="billing_city" class="wpcf7-form-control required" placeholder="עיר" />
							</span>
							<span class="wpcf7-form-control-wrap">
								<input type="text" name="billing_address_1" class="wpcf7-form-control required" placeholder="רחוב" />
							</span>
							<span class="wpcf7-form-control-wrap w50left">
								<input type="tel" name="pobox" class="wpcf7-form-control required digits" placeholder="תיבת דואר" />
							</span>
							<span class="wpcf7-form-control-wrap w50right">
								<input type="tel" name="billing_postcode" class="wpcf7-form-control required digits" placeholder="מיקוד" />
							</span>
							<span class="wpcf7-form-control-wrap">
								<input type="tel" name="billing_phone" class="wpcf7-form-control required digits" placeholder="טלפון נייד" />
							</span>
							<span class="regPopNextBtns w50left">
								<button type="button" class="wpcf7-form-control wpcf7-submit js_nextRegPopStep">המשך</button>
							</span>
							<span class="regPopNextBtns w50right">
								<button type="button" class="wpcf7-form-control wpcf7-submit transparentBtn js_skipRegPopStep">
									דלג
									<svg width="7" height="12" viewBox="0 0 7 12" fill="none" class="btnArrow1" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M1.46081 12L-5.71619e-08 10.6923L4.33192 6L-4.67375e-07 1.30771L1.46081 -6.38539e-08L7 6L1.46081 12Z" fill="#35563C"/>
									</svg>
								</button>
							</span>
						</p>
					</form>
				</div>
				<ul class="regSteps">
					<li><button type="button" class="regStepsBtn active"></button></li>
					<li><button type="button" class="regStepsBtn active"></button></li>
					<li><button type="button" class="regStepsBtn"></button></li>
				</ul>
			</div>
			<div class="regPopSteps">
				<div class="thanksStep">
					<div class="thanksStepMiddleText">
						<img src="<?php echo THEME_URI; ?>/assets/images/envelope.png" alt="" height="70" />
						<div class="thanksStepTitle">
							<?php esc_html_e( 'מזל טוב!', 'supherb' ); ?>
						</div>
						<div class="thanksStepText">
							<?php esc_attr_e( 'שלחנו הודעת אישור לכתובת הדוא״ל', 'supherb' ); ?>
							<b></b>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
