<?php
namespace epiphyt\Form_Block\submissions;

use DateTimeImmutable;
use epiphyt\Form_Block\form_data\Data;

/**
 * Represents a form submission.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission {
	/**
	 * @var		array{fields: mixed[], files: array{local: array{array{filename?: string, hash?: string, path?: string, url?: string}}, validated: array{field_name: string, name: string, path: string, size: int, type: string}|array{}}|array{}, files_local?: array{array{filename?: string, hash?: string, path?: string, url?: string}}, raw: array<string, mixed[]|array>} Submission data
	 */
	private array $data = [
		'fields' => [],
		'files' => [],
		'raw' => [
			'_FILES' => [],
			'_POST' => [],
		],
	];
	
	/**
	 * @var		?\DateTimeImmutable Submission date
	 */
	private ?DateTimeImmutable $date = null;
	
	/**
	 * @var		string Submission form ID
	 */
	private string $form_id = '';
	
	/**
	 * Submission constructor.
	 * 
	 * @param	string	$form_id Form ID
	 * @param	array{fields: mixed[], files: array{local: array{array{filename?: string, hash?: string, path?: string, url?: string}}, validated: array{field_name: string, name: string, path: string, size: int, type: string}|array{}}, files_local: array{array{filename?: string, hash?: string, path?: string, url?: string}}}	$data Submission data
	 */
	public function __construct( string $form_id, array $data ) {
		$this->data = [
			'fields' => $data['fields'],
			'files' => $data['files'],
			'files_local' => $data['files_local'],
			'raw' => [
				// phpcs:disable WordPress.Security.NonceVerification.Missing
				'_FILES' => $_FILES,
				'_POST' => $_POST,
				// phpcs:enable WordPress.Security.NonceVerification.Missing
			],
		];
		$this->date = new DateTimeImmutable( 'now', \wp_timezone() );
		$this->form_id = $form_id;
		
		/**
		 * Filter form submission data.
		 * 
		 * @param	mixed[]	$submission_data Submission data
		 * @param	mixed[] $data Field and files data from the request
		 * @param	string	$form_id Form ID
		 */
		$this->data = (array) \apply_filters( 'form_block_submission', $this->data, $data, $form_id );
	}
	
	/**
	 * Get submission data.
	 * 
	 * @param	string	$name Data name
	 * @param	string	$type Data type to get, 'fields' or 'files'
	 * @return	mixed Data for this entry, null if not available
	 */
	public function get_data( string $name = '', string $type = '' ): mixed {
		if ( empty( $name ) ) {
			return $this->data;
		}
		
		if ( $name === 'form_id' ) {
			return $this->form_id;
		}
		else if ( $name === 'date' ) {
			return $this->get_date();
		}
		else if ( $name === 'files_local' ) {
			return $this->data['files_local'] ?? null;
		}
		
		if ( ! isset( $this->data[ $type ][ $name ] ) ) {
			return null;
		}
		
		return $this->data[ $type ][ $name ];
	}
	
	/**
	 * Get submission date.
	 * 
	 * @param	string	$format Date format
	 * @return	string Date formatted string
	 */
	public function get_date( string $format = '' ): string {
		if ( $this->date === null ) {
			return '';
		}
		
		if ( empty( $format ) ) {
			$format = \get_option( 'date_format' ) . ' ' . \get_option( 'time_format' );
		}
		
		return \wp_date( $format, $this->date->getTimestamp() );
	}
	
	/**
	 * Get submission date object.
	 * 
	 * @return	?\DateTimeImmutable Date Object
	 */
	public function get_date_object(): ?\DateTimeImmutable {
		return $this->date;
	}
	
	/**
	 * Get a form data field.
	 * 
	 * @param	string	$field Field to get
	 * @return	mixed Form field data
	 */
	public function get_form_data( string $field ): mixed {
		$form_data = Data::get_instance()->get( $this->form_id );
		
		return $form_data[ $field ] ?? null;
	}
	
	/**
	 * Get raw submission data.
	 * 
	 * @param	string	$name Data name
	 * @param	string	$type Data type to get, 'fields' or 'files'
	 * @return	mixed Raw data for this entry, null if not available
	 */
	public function get_raw( string $name, string $type = 'fields' ): mixed {
		$data_type = '';
		
		if ( $type === 'fields' ) {
			$data_type = '_POST';
		}
		else if ( $type === 'files' ) {
			$data_type = '_FILES';
		}
		
		if ( empty( $data_type ) ) {
			return [];
		}
		
		return $this->data['raw'][ $data_type ][ $name ] ?? null;
	}
	
	/**
	 * Search in a form submission.
	 * 
	 * @param	string	$term Search term
	 * @param	array{data: mixed[], date: string, id: string}|null	$data Data to search in
	 * @return	bool Whether search term could be found in submitted data
	 */
	public function search( string $term, ?array $data = null ): bool {
		if ( $data === null ) {
			$data = $this->data['fields'];
		}
		
		$found = false;
		
		foreach ( $data as $field_value ) {
			if ( \is_countable( $field_value ) ) {
				$found = $this->search( $term, $field_value );
				
				if ( $found ) {
					return $found;
				}
			}
			else if ( \is_string( $field_value ) ) {
				$found = \str_contains( $field_value, $term );
			}
			
			if ( $found ) {
				return $found;
			}
		}
		
		return $found;
	}
}
