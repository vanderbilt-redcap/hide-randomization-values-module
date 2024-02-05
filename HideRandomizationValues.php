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

    function redcap_every_page_top($project_id) {
        if(strpos($_SERVER['REQUEST_URI'],'/DataExport/index.php') !== false) {
            if (isset($_GET['report_id'])) {
                if ($_GET['report_id'] == "ALL") {
                    $redirectURL = APP_PATH_WEBROOT_FULL. "redcap_v" .REDCAP_VERSION."/DataExport/index.php?pid=".$project_id;
                    ?>
                    <script>
                        window.location = '<?php echo $redirectURL; ?>';
                    </script>
                    <?php
                }
            }
            else {
                ?>
                <script type="text/javascript">
                    $(document).ready(function() {
                        waitForElementToRemove();
                    });
                    function waitForElementToRemove() {
                        var target = $('table#table-report_list tr#reprow_ALL')[0];
                        if(!target) {
                            //The node we need does not exist yet.
                            //Wait 500ms and try again
                            window.setTimeout(waitForElementToRemove,10);
                            return;
                        }
                        $('table#table-report_list tr#reprow_ALL').remove();
                    }
                </script>
                <?php
            }
        }
    }

	function redcap_data_entry_form_top($project_id){
		$results = $this->query("select target_field from redcap_randomization where project_id = ?", [$project_id]);
		$row = $results->fetch_assoc();
		$field = @$row['target_field'];

		if(empty($field)){
			// Randomization must not be enabled on this project.
			return;
		}

		?>
		<style>
			#form #<?=$field?>-tr
			{
				td.data, /* desktop */
				span[data-kind=field-value] /* mobile */ {
					input,
					select,
					label{
						display: none;
					}
				} 

				a{
					/* We use 'visibility' instead of 'display' here so that the row remains the same height. */
					visibility: hidden;
				}
			}
		</style>
		<?php
	}
}