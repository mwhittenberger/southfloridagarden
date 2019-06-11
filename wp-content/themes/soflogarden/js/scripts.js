(function ($, root, undefined) {

	$(function () {

		'use strict';

		//add images to add plant select element
		function formatPlant (plant) {
			if (!plant.id) {
				return plant.text;
			}

			if(plant.text != 'Choose a Plant') {
				var $plant = $(
					'<span><img src="' + plant.element.getAttribute('plantImg') + '" class="img-plant" /> ' + plant.text + '</span>'
				);
				return $plant;
			}

		};

		//add images to add plant select element 2
		$(document).ready(function() {
			$('.choose-plant').select2({
				templateResult: formatPlant,
				dropdownParent: $("#addPlantModal")
			});
		});

		//on selection of plant in add plant modal, get varieties for that plant
		$('#addPlantModal').on('select2:select', function (e) {
			var data = e.params.data;
			console.log(data['id']);
			var thePlant = data['id'];

			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "auto_complete_plant_variety", plant : thePlant},
				success: function(response) {
					if(response.type == "success") {
						console.log("Success");
						console.log(response.varieties);
						var varietiesArray = response.varieties.split(", ");
						console.log(varietiesArray);
						autocomplete(document.getElementById("plant_variety"), varietiesArray);
					}
					else {
						console.log("Epic Fail");
					}
				}
			})
		});

		//on click of add plant modal button, submit form
		/*$('#add-plant').click(function() {
			console.log('submit form');
			$('#add_a_plant_to_location').submit();


		});*/

		//on submission of add plant form
		$('#add_a_plant_to_location').submit(function(e){
			e.preventDefault();
				var thePlant = $('#choose-plant').val();
				var thePlantVariety = $('#plant_variety').val();
				var thePlantQuantity = $('#num_of_plants').val();
				var thePlantStatus = $('#plant_status').val();
				var thePlantNotes = $('#notes').val();
				var thePlantSource = $('#source').val();
				var theLocation = $('#location').val();


				jQuery.ajax({

					type : "post",
					dataType : "json",
					url : myAjax.ajaxurl,
					data : {action: "add_plant_to_location", notes : thePlantNotes, plant : thePlant, variety : thePlantVariety, quantity : thePlantQuantity, status : thePlantStatus, source : thePlantSource, location : theLocation},
					success: function(response) {
						if(response.type == "success") {
							console.log("Success");
							window.location = '/my-garden/';
						}
						else {
							console.log("Epic Fail");
						}
					}
				})

		});

		//set value of location for the added plant
		$('[data-toggle="modal"]').on('click', function () { // Had to use "on" because it was ajax generated content (just an example)
			console.log($(this).attr('data-locationID'));
			$('#location').val($(this).attr('data-locationID'));

			jQuery.ajax({

				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "auto_complete_plant_source", user: $('#user-id').attr('logged-user')},
				success: function(response) {
					if(response.type == "success") {
						console.log("Success");
						var sourcesArray = response.sources.split(", ");
						console.log(sourcesArray);
						autocomplete(document.getElementById("source"), sourcesArray);
					}
					else {
						console.log("Epic Fail");
					}
				}
			})
		});

		//fancy nav
		$('#menuToggle input').change(function() {
			var menuHeight = $('#menu-primary').height() + 20;
			if(this.checked) {
				$('.fancy-nav').css('height',menuHeight);
				$('#menuToggle').addClass('cross');
			}
			else {
				$('.fancy-nav').css('height','0');
				$('#menuToggle').removeClass('cross');
			}
		});

		//show the form to add a new planting location
		$('.addone').click(function() {
			var menuHeight = $('.hidden-inner').height() + 20;
			if($('.hidden-add').height() == 0) {
				$('.hidden-add').css('height',menuHeight);
			}
			else {
				$('.hidden-add').css('height','0');
			}
		});

		//show the element to move a plant to a new location
		$('.move-plant').click(function() {
			jQuery.ajax({
				context: this,
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "get_locations", user: $('#user-id').attr('logged-user'), exception: $(this).attr('garden_location')},
				success: function(response) {
					if(response.type == "success") {
						console.log("Success");
						var locationsArray = response.locations;

						$(this).parent().next().find('select').empty();

						$(this).parent().next().find('select').append('<option>Specify the new location</option>');

						for (var index = 0; index < locationsArray.length; index++) {
							$(this).parent().next().find('select').append('<option value="'+locationsArray[index]['gl_id']+'">'+locationsArray[index]['gl_name']+'</option>');
						}

						$(this).parent().next().css('height','50px');
						$(this).parent().next().css('padding','10px');
					}
					else {
						console.log("Epic Fail");
					}
				}
			})

		});

		//move a planted plant
		$('.new_plant_location').change(function() {
			if($(this).val() != '') {
				jQuery.ajax({
					context: this,
					type : "post",
					dataType : "json",
					url : myAjax.ajaxurl,
					data : {action: "tweak_post", post_id: $(this).next().val(), new_garden_location: $(this).val(), post_action: 'moveplant'},
					success: function(response) {
						if(response.type == "success") {
							console.log("Success");
							window.location = '/my-garden/';

						}
						else {
							console.log("Epic Fail");
						}
					}
				});
			}

		});

		jQuery(".update-plant").click( function(e) {
			var plantID = $(this).attr('data-post_id');
			var plantTypeID = $(this).attr('plant_type_id');




			jQuery.ajax({
				context: this,
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "get_plant_details", user: $('#user-id').attr('logged-user'), plant_id: plantID, plant_type_id: plantTypeID},
				success: function(response) {
					if(response.type == "success") {
						console.log("Success");
						console.log(response);

						$('#name_plant_type').text(response.plant_type);
						$('#name_plant_marker').text(response.plant_marker);

						$('#input_3_1').val(response.plant_variety);
						var varietiesArray = response.varieties.split(", ");
						console.log(varietiesArray);
						autocomplete(document.getElementById("input_3_1"), varietiesArray);

						$('#input_3_2').val(response.plant_quantity);
						$('#input_3_3').val(response.plant_notes);
						$('#input_3_4').val(response.plant_status);
						$('#input_3_5').val(response.plant_source);
						var sourcesArray = response.sources.split(", ");
						autocomplete(document.getElementById("input_3_5"), sourcesArray);
						$('#input_3_6').val(response.plant_germination);
						$('#input_3_7').val(response.plant_germination_rate);
						$('#input_3_8').val(response.plant_germination_date1);
						$('#input_3_9').val(response.plant_germination_date2);


						$('#updatePlantModal').modal('show');

					}
					else {
						console.log("Epic Fail");
					}
				}
			});
		});

		//ajax call for tweaking a location
		jQuery(".action-button").click( function(e) {

			e.preventDefault();
			var post_id = $(this).attr("data-post_id");
			var post_action = $(this).attr("data-action");
			var post_title = $('#post_title').val();
			var post_content = $('#post_content').val();
			var stop_flag = 0;

			if(post_action == 'delete') {
				console.log('deleting');
				let answer = confirm("Are you sure you want to remove this item?");
				if (answer) {
					stop_flag = 0;
				}
				else {
					stop_flag = 1;
				}
			}

			if(stop_flag == 0)
			{
				jQuery.ajax({
					type : "post",
					dataType : "json",
					url : myAjax.ajaxurl,
					data : {action: "tweak_post", post_id : post_id, post_action: post_action},
					success: function(response) {
						if(response.type == "success") {
							console.log("Success");
							if(response.dothis == "refresh") {
								location.reload();
							}
						}
						else {
							console.log("Epic Fail");
						}
					}
				});
			}


		});


		$('#accordion').on('shown.bs.collapse', function () {

			var panel = $(this).find('.in');

			$('html, body').animate({
				scrollTop: panel.offset().top
			}, 500);

		});

		$('.page-template-template-plants-list .panel').on('show.bs.collapse', function () {
			$(this).find('.plant-image').attr('src', $(this).find('.plant-title-wrap').attr('data-bg'));
			/*var top = $(this).find('.panel-body' ).offset().top;
			console.log(top);
			$('html, body').animate({
				scrollTop: (top)
			},200);*/

		})

	});

})(jQuery, this);

function autocomplete(inp, arr) {
	/*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
	var currentFocus;
	/*execute a function when someone writes in the text field:*/
	inp.addEventListener("input", function(e) {
		var a, b, i, val = this.value;
		/*close any already open lists of autocompleted values*/
		closeAllLists();
		if (!val) { return false;}
		currentFocus = -1;
		/*create a DIV element that will contain the items (values):*/
		a = document.createElement("DIV");
		a.setAttribute("id", this.id + "autocomplete-list");
		a.setAttribute("class", "autocomplete-items");
		/*append the DIV element as a child of the autocomplete container:*/
		this.parentNode.appendChild(a);
		/*for each item in the array...*/
		for (i = 0; i < arr.length; i++) {
			/*check if the item starts with the same letters as the text field value:*/
			if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
				/*create a DIV element for each matching element:*/
				b = document.createElement("DIV");
				/*make the matching letters bold:*/
				b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				b.innerHTML += arr[i].substr(val.length);
				/*insert a input field that will hold the current array item's value:*/
				b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				/*execute a function when someone clicks on the item value (DIV element):*/
				b.addEventListener("click", function(e) {
					/*insert the value for the autocomplete text field:*/
					inp.value = this.getElementsByTagName("input")[0].value;
					/*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
					closeAllLists();
				});
				a.appendChild(b);
			}
		}
	});
	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener("keydown", function(e) {
		var x = document.getElementById(this.id + "autocomplete-list");
		if (x) x = x.getElementsByTagName("div");
		if (e.keyCode == 40) {
			/*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 38) { //up
			/*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
			}
		}
	});
	function addActive(x) {
		/*a function to classify an item as "active":*/
		if (!x) return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length) currentFocus = 0;
		if (currentFocus < 0) currentFocus = (x.length - 1);
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add("autocomplete-active");
	}
	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}
	function closeAllLists(elmnt) {
		/*close all autocomplete lists in the document,
        except the one passed as an argument:*/
		var x = document.getElementsByClassName("autocomplete-items");
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}
	/*execute a function when someone clicks in the document:*/
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}
