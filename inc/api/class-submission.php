<?php
declare(strict_types = 1);

namespace epiphyt\Form_Block\api;

use epiphyt\Form_Block\submissions\Submission_Handler;
use WP_REST_Controller;

/**
 * REST API endpoint to handle form submissions.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission extends WP_REST_Controller {
	/**
	 * Initialize functions.
	 */
	public function init(): void {
		\add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function delete_item( $request ) {
		$item = $this->prepare_item_for_database( $request );
		list( $form_id, $submission_key ) = \explode( '/', $item['id'] );
		$deleted = Submission_Handler::delete_submission( $form_id, (int) $submission_key );
		
		if ( ! $deleted ) {
			return new \WP_Error(
				'rest_could_not_delete',
				\__( 'Submission could not be deleted.', 'form-block' ),
				[ 'status' => 400 ]
			);
		}
		
		$data = [
			'data' => [
				'id' => $item['id'],
			],
			'message' => \__( 'Submission deleted successfully.', 'form-block' ),
			'success' => true,
		];
		
		return $this->prepare_item_for_response( $data, $request );
	}
	
	/**
	 * Check if a given request has access to test items.
	 * 
	 * @param	\WP_REST_Request	$request Request object
	 * @return	bool Whether the request has access to test items
	 */
	public function delete_item_permissions_check( $request ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint, SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		return \current_user_can( 'manage_options' );
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}
		
		$this->schema = [
			'$schema' => 'http://json-schema.org/schema#',
			'properties' => [],
			'title' => 'form-block-submission',
			'type' => 'object',
		];
		
		return $this->add_additional_fields_schema( $this->schema );
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function prepare_item_for_database( $request ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint, SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
		$data = [];
		$params = $request->get_params();
		
		if ( isset( $params['id'] ) ) {
			$data['id'] = (string) $params['id'];
		}
		
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function prepare_item_for_response( $item, $request ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint, SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
		return \rest_ensure_response( $this->add_additional_fields_to_object( $item, $request ) );
	}
	
	/**
	 * Register REST API routes.
	 */
	public function register_routes(): void {
		\register_rest_route(
			'form-block/v1',
			'submission/delete/(?P<id>([0-9a-f]{8}\-[0-9a-f]{4}\-4[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}\/\d+))',
			[
				'args' => [
					'id' => [
						'description' => \esc_html__( 'The ID of the submission.', 'form-block' ),
						'required' => true,
						'type' => 'string',
					],
				],
				'callback' => [ $this, 'delete_item' ],
				'methods' => \WP_REST_Server::DELETABLE,
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'schema' => $this->get_item_schema(),
			]
		);
	}
}
