<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
include 'token.php';
$csrf = new csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>GoCal</title>
	<link rel="stylesheet" type="text/css" href="calendar.css">
	<link rel="stylesheet" type="text/css" href="header.css">
	<?php if($valid_user) : ?>
	<script src="jquery-2.1.4.min.js"></script>
	<script src="jquery-ui.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="date.js"></script>
	<script type="text/javascript">
	var currentPageMonth;
	var firstDayOfMonth;
	$(function() {
		$("#share_div").hide();
		$("#submit_event_div").hide();
		$("#view_event_div").hide();
		$("#share_close").click(function(){$("#share_div").hide();});
		$("#submit_event_close").click(function(){$("#submit_event_div").hide();});
		$("#view_event_close").click(function(){$("#view_event_div").hide();});
		currentPageMonth = new Date();
		makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
		$("#prevmon").click(function(){makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth()-1);});
		$("#nextmon").click(function(){makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth()+1);});
		$("#submit_new_event").click(function(){showEventSubmit();});
		$("#vedeventedit").click(function(){submitEdit();});
		$("#vedeventdelete").click(function(){deleteEvent();});
		$("#vedeventsharebut").click(function(){shareEvent();});
		$("#share_calendar").click(function(){viewEditShareSettings();});
		$("#picknone").click(function(){updateCategory("N");});
		$("#pickper").click(function(){updateCategory("P");});
		$("#picksch").click(function(){updateCategory("S");});
		$("#picknbus").click(function(){updateCategory("B");});
	});
function makeCalStruct(year, month){
	currentPageMonth = new Date(year, month);
	$inputMonth = new Date(year, month);
	$currentMonth = new Month($inputMonth.getFullYear(), $inputMonth.getMonth());
		//Display current "month year" on top of page
		$toHeader = $.datepicker.formatDate('MM yy', new Date($currentMonth.year, $currentMonth.month));
		$("#caldate").text($toHeader);
		//Display dates in calendar
		firstDayOfMonth = $currentMonth.getDateObject(1).getDay();
		$lengthOfMonth = Date.getDaysInMonth($currentMonth.year, $currentMonth.month);
		for(var i = 1; i <= 42; i++){
			$("#day" + i + " .dayofmonth").empty();
			$("#day" + i + " ul").empty();
		}
		for (var j = 1; j <= $lengthOfMonth; j++) {
			$objDay = "day" + (firstDayOfMonth + j);
			$("#"+ $objDay + " .dayofmonth").text(j);
		}
		if((firstDayOfMonth + $lengthOfMonth) <= 35){
			$("#calweek6").hide();
		}
		else{
			$("#calweek6").show();
		}
		populateCalendar(year, month, $lengthOfMonth);
	}
	function populateCalendar(year, month, lengthOfMonth){
		for(var i = 1; i <= lengthOfMonth; i++){
			$.ajax({
				url: 'getevents.php',
				type: 'post',
				data: {'year' : year, 'month': month, 'day' : i},
				success: function(data) {
					$daysEvents = jQuery.parseJSON(data);
					$.each($daysEvents, function(key, value){
						$temp = key;
						$id = value[0];
						$eventUser = value[1];
						$eventName = value[4];
						$eventDescription = value[5];
						$eventDate = value[6];
						$valid_ed = value[7];
						$cat = value[8];
						$category = "";
						switch($cat) {
							case 'P':
							$category = " (Personal)";
							break;
							case 'S':
							$category = " (School)";
							break;
							case 'B':
							$category = " (Business)";
							break;
							default:
							$category = "";
						}
						var date = $eventDate.split('/');
						$month = (parseInt(date[0], 10) + 1);
						$day = date[1];
						$year = date[2];
						$objDay = (firstDayOfMonth + parseInt(value[2], 10));
						$displayTime = new Date(year, month, parseInt(value[2], 10), parseInt(value[3], 10)).toString("hh:mm tt");
						$storeTime = value[3];
						$hiddens = '<input type="hidden" id="eventId" value="' + $id + '"><input type="hidden" id="eventCat" value="' + $category + '"><input type="hidden" id="validED" value="' + $valid_ed + '"><input type="hidden" id="eventUser" value="' + $eventUser + '"><input type="hidden" id="eventName" value="' + $eventName + '"><input type="hidden" id="eventDescription" value="' + $eventDescription + '"><input type="hidden" id="eventDate" value="' + $month + '/' + $day + '/' + $year + '"><input type="hidden" id="eventTime" value="' + $storeTime + '">';
						$('<li id="Event' + $id + '" class="event">' + $hiddens + '<div id="tagline">' + value[4] + '<br>' + $displayTime + '</div></li>').appendTo("#day"+ $objDay + " ul");
						$indic = 'li#Event' + $id;
						$("#day"+ $objDay + " ul").on("click", $indic, function(){
							$("#view_event_div").show();
							viewEvent($(this).find('#eventId').val(), $(this).find('#eventDate').val(), $(this).find('#eventTime').val(), $(this).find('#eventName').val(), $(this).find('#eventDescription').val(),$(this).find('#validED').val(), $(this).find('#eventCat').val());
						});
					});
}
});
}
}
function showEventSubmit(){
	$("#submit_event_div").show();
	$("#submitevent").click(function(){submitEvent();});
}
function submitEvent(){
	$eventName = $("#submiteventname").val();
	$eventDate = $("#submiteventdate").val();
	$eventTime = $("#submiteventtime").val();
	$eventDescription = $("#submiteventdescription").val();
	$("#submitError").text("");
	$dateOk = false;
	$timeOk = false;
	var dateRegex = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;
	var timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0][0]$/;
	$dateOk = dateRegex.test($eventDate);
	$timeOK = timeRegex.test($eventTime);
	if($dateOk === true && $timeOK === true && $eventName !== "" && $eventDescription !== ""){
		var date = $eventDate.split('/');
		$month = (parseInt(date[0], 10) - 1);
		$day = date[1];
		$year = date[2];
		$.ajax({
			url: 'addevent.php',
			type: 'post',
			data: {'year' : $year, 'month': $month, 'day' : $day, 'time' : $eventTime, 'name' : $eventName, 'description' : $eventDescription},
			success: function(data) {
				if(data == "Success"){
					alert("Event Added");
					$("#submiteventname").val('');
					$("#submiteventdate").val('');
					$("#submiteventtime").val('');
					$("#submiteventdescription").val('');
					$("#submit_event_div").hide();
					makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
				}
			}
		});
	}
	else{
		if($timeOk === false){
			$("#submitError").text("ERROR: The time you entered is not valid; use times 00:00:00 to 23:59:00");
		}
		if($dateOk === false){
			$("#submitError").text("ERROR: The date you entered is not valid; use dates 00/00/0000 to 12/31/9999");
		}else{
			$("#submitError").text("ERROR: Please make sure all event fields are filled out correctly");
		}
	}
}
function viewEvent(id, date, time, name, description, valid, category){
	$("#vedid").val(id);
	$("#vedeventcategory").text(category);
	$("#vedeventdate").val(date);
	$("#vedeventtime").val(time);
	$("#vedeventname").val(name);
	$("#vedeventdescription").val(description);
	$("#viewError").text("");
	if(valid == 1){
		$("#vedeventedit").show();
		$("#vedeventdelete").show();
		$("#pickcategory").show();
	}
	else{
		$("#vedeventedit").hide();
		$("#vedeventdelete").hide();
		$("#pickcategory").hide();
	}
}
function submitEdit(){
	$eventId = $("#vedid").val();
	$eventName = $("#vedeventname").val();
	$eventDate = $("#vedeventdate").val();
	$eventTime = $("#vedeventtime").val();
	$eventDescription = $("#vedeventdescription").val();
	$("#viewError").text("");
	$dateOk = false;
	$timeOk = false;
	var dateRegex = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;
	var timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0][0]$/;
	$dateOk = dateRegex.test($eventDate);
	$timeOK = timeRegex.test($eventTime);
	if($dateOk === true && $timeOK === true){
		var date = $eventDate.split('/');
		$month = (parseInt(date[0], 10) - 1);
		$day = date[1];
		$year = date[2];
		$token = "<?php echo($csrf->get_token()); ?>";
		$.ajax({
			url: 'editevent.php',
			type: 'post',
			data: {'<?php echo($csrf->get_token_id()); ?>' : $token, 'id' : $eventId, 'year' : $year, 'month': $month, 'day' : $day, 'time' : $eventTime, 'name' : $eventName, 'description' : $eventDescription},
			success: function(data) {
				if(data == "Success"){
					alert("Event Edited");
					$("#vedid").val('');
					$("#vedeventname").val('');
					$("#vedeventdate").val('');
					$("#vedeventtime").val('');
					$("#vedeventdescription").val('');
					$("#viewError").text("");
					$("#view_event_div").hide();
					makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
				}
			}
		});
	}
	else{
		if($timeOk === false){
			$("#viewError").text("ERROR: The time you entered is not valid; use times 00:00:00 to 23:59:00");
		}
		if($dateOk === false){
			$("#viewError").text("ERROR: The date you entered is not valid; use dates 00/00/0000 to 12/31/9999");
		}else{
			$("#viewError").text("ERROR: Please make sure all event fields are filled out correctly");
		}
	}
}
function deleteEvent(){
	$eventId = $("#vedid").val();
	$eventName = $("#vedeventname").val();
	$eventDate = $("#vedeventdate").val();
	$confirmDelete = confirm("confirm: delete event " + $eventName + " on " + $eventDate);
	if($confirmDelete){
		$.ajax({
			url: 'deleteevent.php',
			type: 'post',
			data: {'eventId' : $eventId, '<?php echo($csrf->get_token_id()); ?>' : "<?php echo($csrf->get_token()); ?>"},
			success: function(data) {
				if(data == "Success"){
					alert("Event Deleted");
					$("#vedid").val('');
					$("#vedeventname").val('');
					$("#vedeventdate").val('');
					$("#vedeventtime").val('');
					$("#vedeventdescription").val('');
					$("#view_event_div").hide();
					makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
				}
			}
		});
	}
}
function viewEditShareSettings(){
	$("#share_div").show();
	$("#editsharesettings").click(function(){updateShareSettings();});
	$.ajax({
		url: 'getshare.php',
		type: 'post',
		data: {},
		success: function(data) {
			$("#shareusernames").val(data);
		}
	});
}
function updateShareSettings(){
	$usernames = $("#shareusernames").val();
	$("#shareError").text("");
	$userOk = false;
	$userRegex = /^[0-9a-zA-Z]+(,[0-9a-zA-Z]+)*$/;
	$userOk = $userRegex.test($usernames);
	if($userOk){
		$.ajax({
			url: 'updateshare.php',
			type: 'post',
			data: {'shared_usernames' : $usernames},
			success: function(data) {
				alert("Share Settings Updated");
				$("#shareusernames").val('');
				$("#share_div").hide();
				makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
			}
		});
	}
	else{
		$("#shareError").text("ERROR: please enter valid user names separated by commas and no spaces");
	}
}
function updateCategory(category){
	$eventId = $("#vedid").val();
	$.ajax({
		url: 'updatecategory.php',
		type: 'post',
		data: {'eventId' : $eventId, 'category' : category},
		success: function(data) {
			if(data == "Success"){
				alert("Category Updated");
				$("#vedid").val('');
				$("#vedeventname").val('');
				$("#vedeventdate").val('');
				$("#vedeventtime").val('');
				$("#vedeventdescription").val('');
				$("#view_event_div").hide();
				makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
			}
		}
	});
}
function shareEvent(){
	$eventId = $("#vedid").val();
	$eventName = $("#vedeventname").val();
	$eventDate = $("#vedeventdate").val();
	$eventTime = $("#vedeventtime").val();
	$eventDescription = $("#vedeventdescription").val();
	$eventSharee = $("#vedeventshare").val();
	$("#viewError").text("");
	$dateOk = false;
	$timeOk = false;
	$usernameOk = false;
	var dateRegex = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;
	var timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0][0]$/;
	var userRegex = /^[0-9a-zA-Z]*$/;
	$dateOk = dateRegex.test($eventDate);
	$timeOK = timeRegex.test($eventTime);
	$userOk = userRegex.test($eventSharee);
	if($dateOk === true && $timeOK === true && $userOk === true && $eventName !== "" && $eventDescription !== "" && $eventSharee !== ""){
		var date = $eventDate.split('/');
		$month = (parseInt(date[0], 10) - 1);
		$day = date[1];
		$year = date[2];
		$token = "<?php echo($csrf->get_token()); ?>";
		$.ajax({
			url: 'shareevent.php',
			type: 'post',
			data: {'<?php echo($csrf->get_token_id()); ?>' : $token, 'id' : $eventId, 'year' : $year, 'month': $month, 'day' : $day, 'time' : $eventTime, 'name' : $eventName, 'description' : $eventDescription, 'user_name' : $eventSharee},
			success: function(data) {
				if(data == "Success"){
					alert("Event Shared");
					$("#vedid").val('');
					$("#vedeventname").val('');
					$("#vedeventdate").val('');
					$("#vedeventtime").val('');
					$("#vedeventdescription").val('');
					$("#vedeventshare").val('');
					$("#view_event_div").hide();
					makeCalStruct(currentPageMonth.getFullYear(), currentPageMonth.getMonth());
				}
				if(data == "Invalid User"){
					alert("Invalid User Share String");
				}
			}
		});
	}
	else{
		if($timeOk === false){
			$("#viewError").text("ERROR: The time you entered is not valid; use times 00:00:00 to 23:59:00");
		}
		if($dateOk === false){
			$("#viewError").text("ERROR: The date you entered is not valid; use dates 00/00/0000 to 12/31/9999");
		}
		if ($userOk === false || $eventSharee === "") {
			$("#viewError").text("ERROR: please enter a single valid user name to share with");
		}
		else{
			$("#viewError").text("ERROR: Please make sure all event fields are filled out correctly");
		}
	}
}
</script>
<script src="http://classes.engineering.wustl.edu/cse330/content/calendar.js"></script>
<?php endif; ?>
</head>
<body>
	<?php if($valid_user) : ?>
	<div id="share_div">
		<div id="share_close">x</div>
		<h3 id="share_title">Calendar Sharing Settings</h3>
		<h6 id="shareError">-</h6>
		<p>Users Who Can View Your Calendar: <input type="text" id="shareusernames"></p>
		<p><input type="Submit" id="editsharesettings" value="Update Share Settings"></p>
	</div>
	<div id="submit_event_div">
		<div id="submit_event_close">x</div>
		<h3 id="submit_event_title">Add Event</h3>
		<h6 id="submitError">-</h6>
		<p>Name: <input type="text" id="submiteventname"></p>
		<p>Date (mm/dd/yyyy): <input type="text" id="submiteventdate"></p>
		<p>Time (hh:mm:00): <input type="text" id="submiteventtime"></p>
		<p>Description: <input type="text" id="submiteventdescription"></p>
		<p><input type="Submit" id="submitevent" value="Add Event to Calendar"></p>
	</div>
	<div id="view_event_div">
		<div id="view_event_close">x</div>
		<h3 id="view_event_title">View Event<small id="vedeventcategory"></small></h3>
		<h6 id="viewError">-</h6>
		<input type="hidden" id="vedid">
		<p>Name: <input type="text" id="vedeventname"></p>
		<p>Date (mm/dd/yyyy): <input type="text" id="vedeventdate"></p>
		<p>Time (hh:mm:00): <input type="text" id="vedeventtime"></p>
		<p>Description: <input type="text" id="vedeventdescription"></p>
		<p><input type="Submit" id="vedeventedit" value="Change Event Details"><input type="Submit" id="vedeventdelete" value="Delete Event"><input type="Submit" id="vedeventsharebut" value="Share Event"></p>
		<ul id="pickcategory">
			<li>Set Category
				<ul>
					<li id="picknone">None</li>
					<li id="pickper">Personal</li>
					<li id="picksch">School</li>
					<li id="pickbus">Business</li>
				</ul>
			</li>
		</ul>
		<input type="text" id="vedeventshare" placeholder="Sharee Username">
	</div>
	<div class="page_header">
		<input id="submit_new_event" type="button" name="submit" value="Submit New Event">
		<input id="share_calendar" type="button" name="submit" value="Sharing Settings">
		<h2>GoCal: <?php echo $_SESSION['user_id']; ?></h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<div id="calendar">
		<div id="calendarheader">
			<input id="prevmon" type="button" name="PreviousMonth" value="Prev Month">
			<div id="calheaderdate"><h2 id="caldate">-</h2> </div>
			<input id="nextmon" type="button" name="NextMonth" value="Next Month">
		</div>
		<table id="calmonth">
			<tr id="calheader">
				<td>Sunday</td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
				<td>Saturday</td>
			</tr>
			<tr id="calweek1">
				<td id="day1"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day2"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day3"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day4"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day5"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day6"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day7"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
			<tr id="calweek2">
				<td id="day8"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day9"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day10"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day11"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day12"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day13"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day14"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
			<tr id="calweek3">
				<td id="day15"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day16"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day17"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day18"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day19"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day20"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day21"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
			<tr id="calweek4">
				<td id="day22"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day23"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day24"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day25"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day26"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day27"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day28"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
			<tr id="calweek5">
				<td id="day29"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day30"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day31"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day32"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day33"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day34"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day35"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
			<tr id="calweek6">
				<td id="day36"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day37"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day38"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day39"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day40"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day41"><div class="dayofmonth"></div><ul></ul></td>
				<td id="day42"><div class="dayofmonth"></div><ul></ul></td>
			</tr>
		</table>
	</div>
<?php else : ?>
	<div class="page_header">
		<h2>GoCal</h2>
	</div>
	<div class="invalid_user">
		<h4> User Not Logged In </h4>
		<a href="********************************/">Please Return To The Login Page</a>
	</div>
<?php endif; ?>
</body>
</html>