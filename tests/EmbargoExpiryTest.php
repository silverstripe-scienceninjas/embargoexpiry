<?php

/**
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class WorkflowEmbargoExpiryTest extends SapphireTest {

	/**
	 * @var array
	 */
	protected $requiredExtensions = array(
		'SiteTree' => array(
			'WorkflowEmbargoExpiryExtension',
			'Versioned'
		)
	);

	/**
	 * @var array
	 */
	protected $illegalExtensions = array(
		'SiteTree' => array(
			'Translatable',
		)
	);

	public function __construct() {
		if(!class_exists('AbstractQueuedJob')) {
			$this->skipTest = true;
		}
		parent::__construct();
	}

	public function testFutureDatesJobs() {
		$page = new Page();

		$page->PublishOnDate = '2020-01-01 00:00:00';
		$page->UnPublishOnDate = '2020-01-01 01:00:00';

		// Two writes are necessary for this to work on new objects
		$page->write();
		$page->write();

		$this->assertTrue($page->PublishJobID > 0);
		$this->assertTrue($page->UnPublishJobID > 0);
	}

	public function testDesiredRemovesJobs() {
		$page = new Page();

		$page->PublishOnDate = '2020-01-01 00:00:00';
		$page->UnPublishOnDate = '2020-01-01 01:00:00';

		// Two writes are necessary for this to work on new objects
		$page->write();
		$page->write();

		$this->assertTrue($page->PublishJobID > 0);
		$this->assertTrue($page->UnPublishJobID > 0);

		$page->DesiredPublishDate = '2020-02-01 00:00:00';
		$page->DesiredUnPublishDate = '2020-02-01 02:00:00';

		$page->write();

		$this->assertTrue($page->PublishJobID == 0);
		$this->assertTrue($page->UnPublishJobID == 0);
	}
}
