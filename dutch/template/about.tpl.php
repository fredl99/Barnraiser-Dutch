<?php

// ---------------------------------------------------------------------
// This file is part of Dutch
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// --------------------------------------------------------------------

?>

<div id="col_left">
	<?php
	if (isset($uri_routing[1]) && $uri_routing[1] == "policy") {
	?>
	<div class="box" id="id_content_box">
		<div class="box_header"><h1>Our policy</h1></div>
		<div class="box_body">
			<p>Our goal is to create a free space for you to share knowledge with like minded people. We advocate free expression within
the bounderies of Swedish law. The following provides information on how we
interact with you and how we stay within the bounderies of Swedish law.</p>
			
			<h1>Hate speech</h1>
			<p>Hate speech is publicly making statements that threaten or
express disrespect for an ethnic group or similar group regarding their race,
skin colour, national or ethnic origin, faith or sexual orientation. At Barnraiser  
we see ANY website as PUBLIC thus if you publish hate speech anywhere within
any of our web sites we define this as a crime under "hate speech". Any instance of "hate
speech" will be reported to the Police immediately.</p>
			
			
			<h1>Your personal information and privacy</h1>
			<p>All information except your email address is displayed in your profile page. You can choose to either keep this private or reveal it to members or the public (this can be managed from your "account" page).</p>


			<p>We may use your name and email address to contact you for "service purposes". This means that we may contact
you for a number of purposes related to the service that Barnraiser supplies to you.
For example, we may wish to provide you with password reminders or notify you
that the particular service might not be available for a time.</p>
			
			<p>Barnraiser does not ask you for a personal number, credit card
details, information about your family, school or employment. If you see Barnraiser 
ask for such information via email or from an external website please inform us
immediately using the contact form to the right.</p>
			

			<h1>Your profile page and search engines</h1>
			<p>Information from your contributions is displayed in your profile page. This page is collected and cached by search engines. This means that people such as future employers
will be able to find out about what you posted to this web site long after you
forget about it. When it comes to the web we recommend a policy which is "don't
add anything that you don't want someone to see much
later in life".</p>

			<h1>Banning you</h1>
			<p>We don't. We simply ask that you show others the respect we show
you. Welcome to Barnraiser.</p>
		</div>
	</div>
	<?php
	}
	elseif (isset($uri_routing[1]) && $uri_routing[1] == "toc") {
	?>
	<div class="box" id="id_content_box">
		<div class="box_header"><h1>Terms and conditions</h1></div>
		<div class="box_body">
			<p>This agreement takes place immediately when you register with
this service. We may need to change these terms by posting changes on-line. We
will try to inform you if we do by sending you a Barnraiser email, however your
continued use of this service after changes are posted means you agree to be
legally bound by these terms as updated and/or amended.</p>
			
			<p>You agree to use this website only for lawful purposes, and in a
way that does not infringe the rights of, restrict or inhibit anyone else's use
and enjoyment of this website.</p>
			
			<p>You agree that the service is provided "as is" and "is
available" basis without any representations or any kind of warranty.</p>
			
			<p>Under no circumstances will we be liable for any of the following
losses or damage (whether such losses where foreseen, foreseeable, known or
otherwise): (a) loss of data; (b) loss of revenue or anticipated profits; (c)
loss of business; (d) loss of opportunity; (e) loss of goodwill or injury to
reputation; (f) losses suffered by third parties; or (g) any indirect,
consequential, special or exemplary damages arising from the use of this website
regardless of the form of action.
			
			<p>When you add contributions to Barnraiser
they go on a publically available website. Another person viewing
that web page will download it to their computers cache via their web browser. Because of this we
require your permission to distribute it. You agree that give Barnraiser a
royalty-free distribution license for your work.</p>

			<p>If there is any conflict between these terms and anything else in
our service or from what anyone tells you then these terms will over-rule them.
These terms shall be governed by and interpreted in accordance with the laws of
Sweden because Sweden rocks.</p>
			
			<p>On no account are you allowed to
spam or advertise using any non Barnraiser endorsed advertisements unless you are advertising us of course which is perfectly fine:)</p>
		</div>
	</div>
	<?php
	}
	else {
	?>
	<div class="box" id="id_content_box">
		<div class="box_header"><h1>Introducing Dutch</h1></div>
		<div class="box_body">
			<p>"Dutch" is the codename for this piece of software. It is written by <a href="http://www.barnraiser.org/">Barnraiser</a> as part of research to re-think the way we share knowledge on the web.</p>

			<p>Our goal is to create a fluid pool of knowledge shared amoungst interested people based upon them working together in gathering information from the web.</p>
			
			<p>We are gathering initial feedback at the moment and our aim is to open source it when 100 people have contacted us to tell us how they might us it if it were free to them. You can add to that by telling us how you would use it using the form to the right.</p>
			
			<p>Adverts are by Google. We are playing with targetted Adsence adverts based on tags (network name). If we open source this we will also publish information abotu how to add your own advertising mechanism into the back of it.</p>
		</div>
	</div>
	
	<div class="box" id="id_addons_box">
		<div class="box_header"><h1>Adding information to Dutch</h1></div>
		<div class="box_body">
			<p>You may have noticed that Dutch parses certain information for you. This is done through a series of parsers which include:</p>

			<ul>
				<li><b>Digg</b>: You can publish links from <a href="http://www.digg.com/">http://www.digg.com</a> which will be formatted to display the article introduction and picture.</li>
				<li><b>YouTube</b>: You can publish a link from <a href="http://www.youtube.com/">http://www.youtube.com</a> which will be formatted to display the video thumbnail and introduction.</li>
				<li>You can <b>embed videos</b> which will be displayed within your post.</li>
			</ul>
		</div>
	</div>
	<?php }?>
</div>



<div id="col_right">
	<div class="box">
		<div class="box_header"><h1>About us</h1></div>
		<div class="box_body">
			<ul>
				<li><a href="/about">Introducing Dutch</a></li>
				<li><a href="/about/policy">Policy</a></li>
				<li><a href="/about/toc">Terms and conditions</a></li>
			</ul>
		</div>
	</div>

	<?php
	if (!isset($_SESSION['user_id'])) {
	?>
	<form method="post">
	<div class="box">
		<div class="box_header"><h1>Lost password?</h1></div>
		<div class="box_body">
			<?php
			if (isset($new_password)) {
			?>
			<p>
				Your new password has been emailed to you.
			</p>
			<?php
			}
			else {
			?>
			<p>
				Fill in the following details and we will email you a new password.
			</p>
			
			<p>
				<label for="id_dob_year">Memorable date</label><br />
				<select name="dob_year" id="id_dob_year">
					<option value="">Year</option>
					<?php 
						for($i = 2003; $i > 1908; $i--) {
					?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
				</select> -
				<select name="dob_month" id="id_dob_month">
					<option value="">Month</option>
					<option value="01">01</option>
					<option value="02">02</option>
					<option value="03">03</option>
					<option value="04">04</option>
					<option value="05">05</option>
					<option value="06">06</option>
					<option value="07">07</option>
					<option value="08">08</option>
					<option value="09">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
				</select> - 
				<select name="dob_day" id="id_dob_day">
					<option value="">Day</option>
					<option value="01">01</option>
					<option value="02">02</option>
					<option value="03">03</option>
					<option value="04">04</option>
					<option value="05">05</option>
					<option value="06">06</option>
					<option value="07">07</option>
					<option value="08">08</option>
					<option value="09">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select>
			</p>

			<p>
				<label for="id_email">Email</label>
				<input type="text" name="new_password_email" id="id_email" />
			</p>
			
			<p class="buttons">
				<input type="submit" name="submit_new_password" value="send" />
			</p>
			<?php }?>
		</div>
	</div>
	</form>
	<?php }?>


	<form method="post">
	<div class="box" id="id_email_contact_box">
		<div class="box_header"><h1>Contact us</h1></div>
		<div class="box_body">
			<?php
			if (isset($contact_email_sent)) {
			?>
				<p>Thank you for contacting us. We'll reply as soon as possible.</p>
			<?php }?>

			<p>
				<label for="id_email_contact">Email</label>
				<input type="text" id="id_email_contact" name="email" value="" />
			</p>

			<p>
				<label for="id_message">Message</label>
				<textarea name="message" id="id_message"></textarea>
			</p>

			<p class="buttons">
				<input type="submit" name="send_email" value="send" />
			</p>
		</div>
	</div>
	</form>
</div>