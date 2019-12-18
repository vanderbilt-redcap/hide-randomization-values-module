<?php
namespace Vanderbilt\HideRandomizationValues;

class HideRandomizationValues extends \ExternalModules\AbstractExternalModule{
	function redcap_every_page_before_render(){
		if(strpos($_SERVER['REQUEST_URI'], '/Randomization/randomize_record.php') !== false && @$_POST['action'] === 'randomize'){
			// The randomization results dialog is being displayed.  Modify it and hide the randomization value.
			?>
			<style>
				#randomizeDialog > .darkgreen td:first-child::after{
					padding-left: 25px;
					content: 'The current record was successfully randomized.';
				}

				#randomizeDialog > .darkgreen td:last-child{
					display: none;
				}
			</style>
			<?php
		}
	}

	function redcap_data_entry_form_top($project_id){
		$results = $this->query("select target_field from redcap_randomization where project_id = $project_id");
		$row = $results->fetch_assoc();
		$field = @$row['target_field'];

		if(empty($field)){
			// Randomization must not be enabled on this project.
			return;
		}

		?>
		<style>
			#form #<?=$field?>-tr td.data input,
			#form #<?=$field?>-tr td.data select,
			#form #<?=$field?>-tr td.data label{
				display: none;
			}
		</style>
		<?php
	}
}