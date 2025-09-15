<?php
namespace epiphyt\Form_Block\submissions;

use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\form_data\Field;
use WP_List_Table;

if ( ! \class_exists( 'WP_List_Table' ) ) {
		require_once \ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Submission list table
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission_List_Table extends WP_List_Table {
	/**
	 * @var		array Table data
	 */
	private array $table_data = [];
	
	/**
	 * Initialize functionality.
	 */
	public function init(): void {
		\add_action( 'form_block_submission_actions', [ self::class, 'set_delete_action' ], 50 );
	}
	
	/**
	 * Get default column values.
	 * 
	 * @param	array{data: mixed[], date: string, id: string}	$item Current item
	 * @param	string	$column_name Column name
	 * @return	string Column value
	 */
	public function column_default( $item, $column_name ): string { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
		switch ( $column_name ) {
			case 'actions':
				return '<div class="form-block__submission--actions">' . \do_action( 'form_block_submission_actions', $item ) . '</div>';
			case 'data':
				$field_output = '';
				$file_output = '';
				$form_id = \substr( $item['id'], 0, \strpos( $item['id'], '/' ) );
				$form_data = Data::get_instance()->get( $form_id );
				
				if ( ! empty( $item['data']['fields'] ) ) {
					$field_output = \trim( Field::get_instance()->get_output( $form_data['fields'], $item['data']['fields'], 0, 'html' ) );
					$field_output = \sprintf(
						'<strong id="%2$s">%1$s</strong>
						<dl aria-labelledby="%2$s">%3$s</dl>',
						\esc_html__( 'Fields', 'form-block' ),
						$item['id'] . '-fields',
						$field_output
					);
				}
				
				if ( ! empty( $item['data']['files'] ) ) {
					$file_output = \sprintf(
						'<strong id="%2$s">%1$s</strong>
						<dl aria-labelledby="%2$s">%3$s</dl>',
						\esc_html__( 'Files', 'form-block' ),
						$item['id'] . '-files',
						\implode( \PHP_EOL, $item['data']['files'] )
					);
				}
				
				$submit_data = Data::get_submit_object_data( $item['data']['raw']['_POST'] );
				$title = \sprintf(
					/* translators: form label */
					'<strong>' . \esc_html__( '%s submitted', 'form-block' ) . '</strong>' . \PHP_EOL,
					\esc_html( $item['label'] ?? \__( 'Contact form', 'form-block' ) )
				);
				
				return \sprintf(
					'%1$s
					<details class="form-block__data-details">
						<summary class="button">%2$s</summary>
						<div class="form-block__field-output">
							%3$s
							%4$s
						</div>
					</details>',
					$title,
					\esc_html__( 'View submitted data', 'form-block' ),
					$field_output,
					$file_output
				);
			case 'source':
				$submit_data = Data::get_submit_object_data( $item['data']['raw']['_POST'] );
				
				return $submit_data['url'] ? '<a href="' . \esc_url( $submit_data['url'] ) . '">' . \esc_html( $submit_data['title'] ) . '</a>' : \esc_html__( 'unknown page', 'form-block' );
			default:
				return (string) $item[ $column_name ] ?? '';
		}
	}
	
	/**
	 * Get table columns.
	 * 
	 * @return	string[] Table columns
	 */
	public function get_columns(): array {
		$columns = [ // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys.IncorrectKeyOrder
			'data' => \__( 'Data', 'form-block' ),
			'source' => \__( 'Source', 'form-block' ),
			'date' => \__( 'Date', 'form-block' ),
			'actions' => \__( 'Actions', 'form-block' ),
		];
		
		/**
		 * Filter submissions columns.
		 * 
		 * @param	string[] List of columns
		 */
		$columns = (array) \apply_filters( 'form_block_submissions_columns', $columns );
		
		return $columns;
	}
	
	/**
	 * Get table data.
	 * 
	 * @return	array{array{data: mixed[], date: string, id: string}} Table data
	 */
	private static function get_data(): array {
		$data = [];
		$search_term = \sanitize_text_field( \wp_unslash( $_POST['s'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$submissions = Submission_Handler::get_submissions();
		
		foreach ( $submissions as $form_id => $form_submissions ) {
			foreach ( $form_submissions as $key => $submission ) {
				if ( ! empty( $search_term ) ) {
					if ( ! $submission->search( $search_term ) ) {
						continue;
					}
				}
				
				$data[] = [
					'data' => $submission->get_data(),
					'date' => $submission->get_date(),
					'id' => $form_id . '/' . $key,
					/* translators: blog name */
					'label' => $submission->get_form_data( 'label' ) ?: \sprintf( \__( 'Form submission', 'form-block' ), \get_bloginfo( 'name' ) ),
				];
			}
		}
		
		return $data;
	}
	
	/**
	 * Get sortable columns.
	 * 
	 * @return	mixed[][] Sortable columns
	 */
	protected function get_sortable_columns(): array {
		$sortable_columns = [
			'date' => [ 'date', true, 'date', \__( 'Date', 'form-block' ), 'desc' ],
		];
		
		/**
		 * Filter submission sortable columns.
		 * 
		 * @param	mixed[]	$sortable_columns Current sortable columns
		 */
		$sortable_columns = (array) \apply_filters( 'form_block_submissions_columns_sortable', $sortable_columns );
		
		return $sortable_columns;
	}
	
	/**
	 * Prepare item data.
	 */
	public function prepare_items(): void {
		$columns = $this->get_columns();
		$this->_column_headers = [
			$columns,
			[],
			$this->get_sortable_columns(),
			'date',
		];
		$this->table_data = self::get_data();
		
		\usort( $this->table_data, [ self::class, 'sort_items' ] );
		
		$current_page = $this->get_pagenum();
		$per_page = $this->get_items_per_page( 'submissions_per_page' );
		$total_items = \count( $this->table_data );
		$this->table_data = \array_slice( $this->table_data, ( $current_page - 1 ) * $per_page, $per_page );
		
		$this->set_pagination_args( [
			'per_page' => $per_page,
			'total_items' => $total_items,
			'total_pages' => (int) \ceil( $total_items / $per_page ),
		] );
		
		$this->items = $this->table_data;
	}
	
	/**
	 * Set delete action.
	 * 
	 * @param	array{data: mixed[], date: string, id: string}	$item Current item
	 */
	public static function set_delete_action( array $item ): void {
		?>
		<button type="button" class="button form-block__delete" data-id="<?php echo \esc_attr( $item['id'] ); ?>">
			<?php
			\printf(
				/* translators: "submission" as screen reader text */
				\esc_html__( 'Delete %s', 'form-block' ),
				'<span class="screen-reader-text">' . \esc_html__( 'submission', 'form-block' ) . '</span>'
			)
			?>
		</button>
		<?php
	}
	
	/**
	 * Sort items.
	 * 
	 * @param	array{data: mixed[], date: string, id: string}	$a First item
	 * @param	array{data: mixed[], date: string, id: string}	$b Second item
	 * @return	int Sorting order of these items
	 */
	private static function sort_items( array $a, array $b ): int {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$orderby = ! empty( $_GET['orderby'] ) ? \sanitize_text_field( \wp_unslash( $_GET['orderby'] ) ) : 'date';
		$order = ! empty( $_GET['order'] ) ? \sanitize_text_field( \wp_unslash( $_GET['order'] ) ) : 'desc';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		
		if ( $orderby === 'date' ) {
			$result = \strtotime( $a[ $orderby ] ) - \strtotime( $b[ $orderby ] );
		}
		else {
			$result = \strnatcmp( $a[ $orderby ], $b[ $orderby ] );
		}
		
		return $order === 'asc' ? $result : -$result;
	}
}
