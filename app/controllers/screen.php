<?php

	class Screen extends Controller {
		public function __construct() {

			$acls = array(
				'allow' => array(
					'*' => '*'
				),
				'deny'  => array(),
				'order' => 'AD' //Allow, Then Deny (Options are "DA" or "AD")
			);
			$this->acl( $acls );

			parent::__construct();

			/* @var $this ->brokers_model Brokers_model */
			$this->loadModel( 'screen_model' );
			$this->loadModel( 'window_model' );
		}

		/**
		 * Default function
		 */
		function index() {
			//request will be routed to display.
			parent::defIndex();
		}

		/**
		 * All search reach here
		 */
		function search() {
			$this->listtable();
		}

		/*
		 * Ajax page view
		 */
		function page() {
			$this->listall();
		}

		/**
		 * Display view without design layout.
		 */
		function listall() {
			parent::defListall();
		}

		/**
		 * Method to retrieve records list table.
		 *
		 * @param int $page page no.
		 *
		 * @return bool|void
		 */
		function listtable( $page = 1 ) {
			$this->loadLibrary( 'pagination.php' );
			$filter = $this->input->request( 'searchq' );
			if ( ! $filter ) {
				$filter = $this->input->request( 'hid-searchq' );
			}
			$cond = '';
			if ( $filter ) {
				$cond = " AND (s.name LIKE '%$filter%' )";
			}

			//{sort
			$sort = '';
			if ( @$this->input->request['searchq-col'] || @$this->input->request['hid-searchq-col'] ) {
				$sort = ' ORDER BY ' . ( ( $this->input->request['searchq-col'] ) ? $this->input->request['searchq-col'] : @$this->input->request['hid-searchq-col'] );
				if ( ( @$this->input->request['searchq-sort'] == 'desc' ) || ( @$this->input->request['hid-searchq-sort'] == 'desc' ) ) {
					$sort .= ' DESC ';
				} else {
					$sort .= ' ASC ';
				}
			}
			if ( ! $sort ) {
				$sort = ' ORDER BY s.id DESC ';
			}


			//Branch only data..
			$branchId = branchEmployee();
			if ( $branchId ) {
				$cond .= " AND s.branch_id='$branchId' ";
			}

			$sql = "SELECT s.*, b.name as branch FROM screen s
				LEFT JOIN branch b ON s.branch_id = b.id
				WHERE s.deleted='0' $cond $sort";

			$sqlcnt = "SELECT COUNT(*) as cnt FROM screen s
			LEFT JOIN branch b ON s.branch_id = b.id
				WHERE s.deleted='0' $cond";

			$url = siteUrl( 'screen/listtable/' );

			$this->vars['pager_url'] = $url;
			if ( $this->ifCsvExport() ) {
				$order = array(
					'Screen' => 'screen',
				);

				$this->exportCsv( $sql, $order );

				return;
			}

			$result = $this->pagination->pager( $sql, $sqlcnt, $url, 'idListArea' . get_class( $this ), $page );
			//render page with default template file.
			parent::defListtable( $result );
		}

		/**
		 * Validation function for add and edit.
		 *
		 * @param string 'e' for edit, 'a' for add
		 *
		 * @return array validation results..
		 */
		function validate( $mode ) {
			$errors            = array();
			$errors['eName']   = '';
			$errors['eBranch'] = '';


			//{ db check conditions
			if ( $mode == 'edit' ) {
				$editId = $this->getArg( 'editId' );
				$eCond  = " ( name = '" . $this->fields['txtName'] . "' AND id != '$editId' AND deleted = 0 ) ";

			} else if ( $mode == 'add' ) {
				$eCond = " ( name = '" . $this->fields['txtName'] . "' AND deleted = 0 ) ";
			}
			//}

			//basic test cases
			if ( ! @$this->fields['txtName'] ) {
				$errors['eName'] = 'Name not specified';
			}
			//db test cases
			if ( ! @$this->fields['selBranch'] ) {
				$errors['eBranch'] = 'Branch not specified';
			}
			//db test cases
			if ( @$this->fields['txtName'] ) {
				if ( $this->screen_model->isExists( $eCond ) ) {
					$errors['eName'] = 'Name already exists';
				}
			}

			return $errors;
		}

		/**
		 * Function to handle add form submition.
		 *
		 * @return boolean true on success
		 */
		private function onAdd() {
			$errors = $this->validate( 'add' );

			//has any error ?
			if ( countReal( $errors ) > 0 ) {
				//then stop here..
				return new JStatus( false, 'Please fix validation errors', $errors );
				//--- END: ----
			}

			$image = $this->upload();

			if(!$this->fields['txtScreenBackground']){
				$background=$image;

			}
			else{
				$background=$this->fields['txtScreenBackground'];
			}
			$this->db->beginTrans();

			$screen = array(
				'name'       => $this->fields['txtName'],
				'branch_id'  => $this->fields['selBranch'],
				'w'          => $this->fields['txtWidth'],//PRM_DEFAULT_SCREEN_WIDTH,
				'h'          => $this->fields['txtHeight'],// PRM_DEFAULT_SCREEN_HEIGHT,filWindowBackground
				'background' => $background,//$_FILES['filWindowBackground']['name']


				//Do other parameters...
			);


			if ( $this->screen_model->insert( $screen ) ) {
				$insId = $this->db->getLastInsertId();
				$this->db->commitTrans();

				return new JStatus( true, 'Successfully saved', array( '__id' => $insId ) );

			}

			$this->db->rollbackTrans();

			return new JStatus( false, 'Unable to save' );
		}

		/**
		 * Shows add form
		 */
		function add() {
			if ( $this->input->post( 'btnSubmit' ) ) {
				$jstat = $this->onAdd();

				if ( $jstat->status ) {
					if ( $this->getArg( 'contentAreaClicked' ) != 'idPopupSubmit' ) {
						ob_start();
						if ( $this->getArg( 'contentAreaClicked' ) == 'idContentAreaSmall' ) {
							$this->view( $jstat->data['__id'] );
						} else {
							$this->page();
						}
						$jstat->data['idContentAreaBig'] = ob_get_clean();
					}
				}
				$this->statusResponse( $jstat );

				return;
			}
			$this->vars['screens'] = $this->getModel( 'screen_model' )->get();

			$this->vars['mode'] = 'add';
			$this->vars['url']  = siteUrl( 'screen/add' );

			$branchId = branchEmployee();
			$where    = array();
			if ( $branchId ) {
				$where = array(
					'id' => $branchId
				);
			}
			$this->vars['branches'] = $this->getModel( 'branch_model' )->getWhereBy( $where );
			parent::defAdd();
		}

		/**
		 * On edit submit
		 */
		private function onEdit( $id ) {
			$errors = $this->validate( 'edit' );
			//has any error ?
			if ( countReal( $errors ) > 0 ) {
				//then stop here..
				return new JStatus( false, 'Please fix validation errors', $errors );
				//--- END ---
			}

			//get id
			$this->db->beginTrans();
			//get current values


			$screen = array(
				'name'      => $this->fields['txtName'],
				'branch_id' => $this->fields['selBranch'],
				//other screen data..
			);

			if ( $this->screen_model->update( $screen, array( 'id' => $id ) ) ) {
				$this->db->commitTrans();

				//return status
				return new JStatus( true, 'screen details updated successfully' );
			}
			$this->db->rollbackTrans();

			return new JStatus( false, 'Unable to update screen details' );
		}

		/**
		 * Edit id
		 *
		 * @param int $id
		 *
		 * @return bool|void
		 */
		function edit( $id ) {
			if ( $this->input->post( 'btnSubmit' ) ) {
				$jstat = $this->onEdit( $id );

				if ( $jstat->status ) {
					ob_start();
					if ( $this->getArg( 'contentAreaClicked' ) == 'idContentAreaSmall' ) {
						$this->view( $id );
					} else {
						$this->page();
					}
					$jstat->data['idContentAreaBig'] = ob_get_clean();
				}
				$this->statusResponse( $jstat );

				return false;
			}

			$this->vars['mode'] = 'edit';
			$this->setArg( 'editId', $id );
			$this->vars['url']      = siteUrl( 'screen/edit' . '/' . $id );
			$this->vars['result']   = $this->screen_model->getDetails( $id );
			$this->vars['branches'] = $this->getModel( 'branch_model' )->get();

			$this->loadView( 'screen_add.php' );
		}

		/**
		 * Display indivitual details
		 */
		function view( $id ) {
			$rec = $this->screen_model->getDetails( $id );
			$this->loadView( 'screen_view.php', array( 'result' => $rec ) );
		}

		/**
		 * Mark a record as deleted.
		 *
		 * @param int $id id to remove
		 *
		 * @return bool
		 */
		function delete( $id, $silent = false ) {

			$sql = "SELECT count(*) as cnt FROM branch WHERE screen_id='$id'";
			$hasLead = $this->db->scalarField($sql) ;
			if( $hasLead )
			{
				if( ! $silent )
				{
					$this->statusResponse( 'Fail', 'Unable to delete, Lead exists.', array('_id' => $id) ) ;
				}
				return false ;
			}
			

			$where = array(
				'id' => $id
			);

			$status = false;

			if ( $this->screen_model->delete( $where ) ) {

				$status = true;
			}

			if ( ! $silent ) {
				$this->statusResponse( ( ( $status ) ? 'OK' : 'Fail' ), ( ( $status ) ? 'Screen deleted successfully' : 'Unable to delete branch group' ), array( '_id' => $id ) );
			}

			return $status;


		}

		/**
		 * Mark a record as deleted.
		 *
		 * @param int $id id to remove
		 *
		 * @return bool
		 */
		function duplicate( $id, $silent = false, $name = '' ) {
			$sql  = "SELECT * FROM screen WHERE id='$id' LIMIT 1";
			$data = $this->db->fetchRow( $sql, 'assoc' );
			//screen
			$sqlw  = "SELECT * FROM window WHERE screen_id='$id' ";
			$dataw = $this->db->fetchRowSet( $sqlw, 'assoc' );

			//prepare screen
			unset( $data['id'] );
			$newName      = ( $name ) ? urldecode( $name ) : $data['name'] . ' Copy';
			$data['name'] = $newName;
			$status       = false;
			if ( is_array( $data ) ) {
				$this->db->beginTrans();
				//insert screen
				$this->screen_model->insert( $data );
				$newId = $this->db->getLastInsertId();
				
				//insert windows
				foreach ( $dataw as $window ) {
					unset( $window['id'] );
					$window['screen_id'] = $newId;
					$window['name']      = $window['name'] . ' Copy';
					$this->window_model->insert( $window );
				}
				$this->db->commitTrans();
				$status = true;
			}
			if ( ! $silent ) {
				$this->statusResponse( ( ( $status ) ? 'OK' : 'Fail' ), ( ( $status ) ? 'Duplicated successfully' : 'Unable to duplicate' ), array( '_id' => $id ) );
			}

			return $status;
		}

		/**
		 * Mark a record as deleted.
		 *
		 * @param int $id id to remove
		 *
		 * @return bool
		 */
		function refresh( $id, $refresh, $silent = false ) {
			$data = array(
				'refresh_dt' => sqlNoQuote( 'NOW()' ),
			);

			$status = $this->screen_model->update( $data, array( 'id' => $id ) );

			if ( $refresh ) {
				if ( ! $silent ) {
					$this->statusResponse( ( ( $status ) ? 'OK' : 'Fail' ), ( ( $status ) ? 'Screen will refresh in few moments' : 'Failed to refresh' ), array( '_id' => $id ) );
				}
			} else {
				if ( ! $silent ) {
					$this->statusResponse( ( ( $status ) ? 'OK' : 'Fail' ), ( ( $status ) ? 'Screen refresh canceled' : 'Failed to canncel screen refresh' ), array( '_id' => $id ) );
				}
			}

			return $status;
		}

		/**
		 * Buld action handler
		 *
		 * @return boolean true on succes
		 */
		function bulkAction() {
			//Select action
			$action = @$this->fields['hidbulkaction'];

			//Validate bulk action
			if ( ! $action ) {
				$this->statusResponse( 'FAIL', 'Unknown action' );

				return false;
			}

			$screen = @$this->fields['cbList'];
			if ( @count( $screen ) < 1 ) {
				$this->statusResponse( 'FAIL', 'There are no screen' );

				return false;
			}

			//Do bulk action
			$msge = '';
			$stat = array( 'action-s' => 0, 'action-f' => 0 );
			switch ( $action ) {
				case 'delete' :
					$msgs = '%s screen(s) deleted.';
					$msge = '%f screen(s) not deleted.';
					foreach ( $screen as $v ) {
						if ( $this->delete( $v, true ) ) {
							$stat['action-s'] ++;
						} else {
							$stat['action-f'] ++;
						}
					}
					break;
			}

			//Format message
			$msg    = '';
			$status = 'FAIL';
			if ( $stat['action-s'] > 0 ) {
				$msg    = str_ireplace( '%s', $stat['action-s'], $msgs );
				$status = 'OK';
			}
			if ( $stat['action-f'] > 0 ) {
				$msg    = $msg . ' ' . str_ireplace( '%f', $stat['action-f'], $msge );
				$status = 'FAIL';
			}

			$this->statusResponse( $status, $msg );
		}

		function upload() {
			//Upload file
			if ( ! empty( $_FILES ) ) {
				if ( ! empty( $_FILES ) ) {
					//get uploaded file extension
					$ext = getFileExtension( $_FILES['filWindowBackground']['name'] );
					$this->loadLibrary( 'upload' );
					$config = array(
						'upload_path' => fileUrl( 'app/data/' ),
						'file_name'   => 'file_' . '_' . getUniqId() . '.' . $ext,
					);


					$uploaded = $this->upload->doUpload( 'filWindowBackground', $config );
					if ( $uploaded ) {
						return "url(".baseUrl( 'app/data/' . $config['file_name'] ).")";
					}
				}
			}
		}
	}

	?>