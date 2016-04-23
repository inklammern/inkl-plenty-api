<?php

namespace Plenty\Api\Entities;

class Order
{

	private $id;
	private $externalId;
	private $status;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExternalId()
	{
		return $this->externalId;
	}

	/**
	 * @param mixed $externalId
	 * @return $this
	 */
	public function setExternalId($externalId)
	{
		$this->externalId = $externalId;
		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param mixed $status
	 * @return $this
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}


}