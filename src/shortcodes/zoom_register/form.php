<?php
/**
 * Registration form and hander
 */
defined( 'ABSPATH' ) || exit;

class ZOOMPRESS_Registration_Form {

	protected $questions = [];

	protected $customQuestions = [];

	public function __construct($webinarQuestions) {
		if (isset($webinarQuestions['questions']) && is_array($webinarQuestions['questions'])) {
			$this->questions = $webinarQuestions['questions'];
		}

		if (isset($webinarQuestions['custom_questions']) && is_array($webinarQuestions['custom_questions'])) {
			$this->customQuestions = $webinarQuestions['custom_questions'];
		}
	}

	public function getTypeRenderer($type) {
		$map = [
			'short' => 'printFieldShort',
			'single_radio' => 'printFieldSingleRadio',
			'single_dropdown' => 'printFieldSingleDropdown',
			'multiple' => 'printFieldMultiple',
		];

		if (!isset($map[$type])) {
			return false;
		}

		return $map[$type];
	}

	public function getSpecialFields() {
		return [
			'first_name' => [
				'type' => 'short',
				'title' => 'First name'
			],
			'last_name' => [
				'type' => 'short',
				'title' => 'Last name'
			],
			'email' => [
				'type' => 'short',
				'title' => 'Your email'
			],
		];
	}

	public function getCommonFields() {
		return [
			'first_name' => [
				'type' => 'short',
				'title' => 'First name'
			],
			'last_name' => [
				'type' => 'short',
				'title' => 'Last name'
			],
			'email' => [
				'type' => 'short',
				'title' => 'Your email'
			],
			'address' => [
				'type' => 'short',
				'title' => 'Address'
			],
			'city' => [
				'type' => 'short',
				'title' => 'City'
			],
			'country' => [
				'type' => 'short',
				'title' => 'Country'
			],
			'zip' => [
				'type' => 'short',
				'title' => 'Zip'
			],
			'phone' => [
				'type' => 'short',
				'title' => 'Phone'
			],
			'industry' => [
				'type' => 'short',
				'title' => 'Industry'
			],
			'org' => [
				'type' => 'short',
				'title' => 'Organization'
			],
			'job_title' => [
				'type' => 'short',
				'title' => 'Job Title'
			],
			'purchasing_time_frame' => [
				'type' => 'multiple',
				'title' => 'Purchasing Time Frame',
				'answers' => [
					'Within a month',
					'1-3 months',
					'4-6 months',
					'More than 6 months',
					'No timeframe',
				]
			],
			'role_in_purchase_process' => [
				'type' => 'single_radio',
				'title' => 'Role In Purchase Process',
				'answers' => [
					'Decision Maker',
					'Evaluator/Recommender',
					'Influencer',
					'Not involved',
				]
			],
			'no_of_employees' => [
				'type' => 'single_dropdown',
				'title' => 'No Of Employees',
				'answers' => [
					'1-20',
					'21-50',
					'51-100',
					'101-500',
					'500-1,000',
					'1,001-5,000',
					'5,001-10,000',
					'More than 10,000',
				]
			],
			'comments' => [
				'type' => 'short',
				'title' => 'Comments'
			],
		];
	}

	public function getFieldLabel($fieldData, $isRequired = false) {
		if (!isset($fieldData['title'])){
			return;
		}

		if ($isRequired) {
			return $fieldData['title'] . ' (required)';
		}

		return $fieldData['title'];
	}

	public function printFieldShort($fieldName, $fieldData, $isRequired = false) {
		$type = 'text';
		$id = uniqid( $fieldName );

		if (in_array($fieldName, ['comments','address'], true)) {
			$type = 'textarea';
		} elseif ($fieldName === 'email') {
			$type = 'email';
		}

		$req = '';
		if ($isRequired) {
			$req = 'required="required"';
		}
		?>
		<div class="zoompress-form-field zoompress-field-field--short">
			<label class="zoompress-form-label" for="zoompress-field-<?php echo $id; ?>"><?php echo $this->getFieldLabel($fieldData, $isRequired); ?></label>
			<?php if ( $type === 'textarea' ) : ?>
				<textarea <?php echo $req; ?> name="<?php echo esc_attr( $fieldName ); ?>" id="zoompress-field-<?php echo $id; ?>" cols="30" rows="3"></textarea>
			<?php else : ?>
				<input <?php echo $req; ?> class="zoompress-input-field zoompress-field-first-name" name="<?php echo esc_attr( $fieldName ); ?>" id="zoompress-field-<?php echo $id; ?>" type="<?php echo $type; ?>" />
			<?php endif; ?>
		</div>
		<?php
	}

	public function printFieldSingleRadio($fieldName, $fieldData, $isRequired = false) {
		if (empty($fieldData['answers']) || !is_array($fieldData['answers'])) {
			return;
		}

		$req = '';
		if ($isRequired) {
			$req = 'required="required"';
		}
		?>
		<div class="zoompress-form-field zoompress-field-field--radio">
			<label class="zoompress-form-label"><?php echo $this->getFieldLabel($fieldData, $isRequired); ?></label>
			<?php foreach ($fieldData['answers'] as $answer ) : $id = uniqid(sanitize_key( $answer )); ?>
				<label for="zoompress-field-<?php echo $id; ?>">
					<input <?php echo $req; ?> id="zoompress-field-<?php echo $id; ?>" type="radio" name="<?php echo esc_attr( $fieldName ); ?>" value="<?php echo esc_attr( $answer ); ?>" /> <?php echo $answer; ?>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public function printFieldSingleDropdown($fieldName, $fieldData, $isRequired = false) {
		if (empty($fieldData['answers']) || !is_array($fieldData['answers'])) {
			return;
		}

		$id = uniqid( $fieldName );
		$req = '';
		if ($isRequired) {
			$req = 'required="required"';
		}
		?>
		<div class="zoompress-form-field zoompress-field-field--dropdown">
			<label class="zoompress-form-label" for="zoompress-field-<?php echo $id; ?>"><?php echo $this->getFieldLabel($fieldData, $isRequired); ?></label>
			<select <?php echo $req; ?> id="zoompress-field-<?php echo $id; ?>" name="<?php echo esc_attr( $fieldName ); ?>">
			<?php foreach ($fieldData['answers'] as $answer ) : ?>
				<option value="<?php echo esc_attr( $answer ); ?>"><?php echo $answer; ?></option>
			<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	public function printFieldMultiple($fieldName, $fieldData, $isRequired = false) {
		if (empty($fieldData['answers']) || !is_array($fieldData['answers'])) {
			return;
		}
		$req = '';
		if ($isRequired) {
			$req = 'required="required"';
		}
		?>
		<div class="zoompress-form-field zoompress-field-field--multiple">
			<label class="zoompress-form-label"><?php echo $this->getFieldLabel($fieldData, $isRequired); ?></label>
			<?php foreach ($fieldData['answers'] as $answer) : $id = uniqid(sanitize_key( $answer )); ?>
			<label for="zoompress-field-<?php echo $id; ?>">
				<input <?php echo $req; ?> id="zoompress-field-<?php echo $id; ?>" type="checkbox" name="<?php echo esc_attr( $fieldName ); ?>" value="<?php echo esc_attr( $answer ); ?>" /> <?php echo $answer; ?>
			</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public function printCommonFields() {
		$requiredFields = wp_list_pluck($this->questions, 'required', 'field_name');
		$fields = array_intersect_key($this->getCommonFields(), $requiredFields);
		$fields = array_merge($this->getSpecialFields(), $fields);

		foreach ($fields as $name => $data) {
			if (!isset($data['type'])) {
				continue;
			}

			$typeRenderer = $this->getTypeRenderer($data['type']);

			if (!method_exists($this, $typeRenderer)) {
				continue;
			}

			call_user_func([$this, $typeRenderer], $name, $data, true);
		}

		return $fields;
	}

	public function printCustomFields() {
		foreach ($this->customQuestions as $question) {
			if (!isset($question['type'])) {
				continue;
			}

			$typeRenderer = $this->getTypeRenderer($question['type']);

			if (!method_exists($this, $typeRenderer)) {
				continue;
			}

			$key = sanitize_key($question['title']);

			call_user_func([$this, $typeRenderer], $key, $question, $question['required']);
		}
	}

	public function printFields() {
		$fields = $this->printCommonFields();
		$this->printCustomFields();

		$f = [
			'q' => $fields,
			'cq' => $this->customQuestions,
		];

		echo '<input type="hidden" name="zoompress_fields" value="'.esc_attr(wp_json_encode($f)).'">';
	}
}
