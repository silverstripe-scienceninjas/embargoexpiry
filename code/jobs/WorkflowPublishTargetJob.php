<?php
/**
 * A queued job that publishes or unpublishes a target at a set date-time
 *
 * @package embargoexpiry
 */
class WorkflowPublishTargetJob extends AbstractQueuedJob {

	public function __construct($obj = null, $type = null) {
		if ($obj) {
			$this->setObject($obj);
			$this->publishType = $type ? strtolower($type) : 'publish';
			$this->totalSteps = 1;
		}
	}

	public function getTitle() {
		return _t('WorkflowPublishTargetJob.SCHEDULEJOBTITLE', "Scheduled $this->publishType of " . $this->getObject()->Title);
	}

	public function process() {
		if ($target = $this->getObject()) {
			if ($this->publishType == 'publish') {
				$target->setIsPublishJobRunning(true);
				$target->dbObject('PublishOnDate')->setValue(false);
				$target->writeWithoutVersion();
				$target->doPublish();
			} else if ($this->publishType == 'unpublish') {
				$target->setIsPublishJobRunning(true);
				$target->dbObject('UnPublishOnDate')->setValue(false);
				$target->writeWithoutVersion();
				$target->doUnpublish();
			}
		}
		$this->currentStep = 1;
		$this->isComplete = true;
	}
}
