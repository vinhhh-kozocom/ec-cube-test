<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Customize\Entity\UserRegisterMetas')) {
    /**
     * UserRegisterMetas
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="user_register_metas")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class UserRegisterMetas extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer")
         *
         * @ORM\Id
         *
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var int
         *
         * @ORM\Column(name="customer_id", type="integer", nullable=true)
         */
        private $customer_id;

        /**
         * @var string
         *
         * @ORM\Column(name="user_ip", type="string", nullable=true)
         */
        private $user_ip;

        /**
         * @var string
         *
         * @ORM\Column(name="user_agent", type="string", nullable=true)
         */
        private $user_agent;

        /**
         * @var int
         *
         * @ORM\Column(name="status", type="integer", nullable=true)
         */
        private $status;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="created_at", nullable=true, type="date")
         */
        private $created_at;

        /**
         * Get id
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set id
         *
         * @param int $id
         *
         * @return UserRegisterMetas
         */
        public function setId($id)
        {
            $this->id = $id;

            return $this;
        }

        /**
         * Set customer_id
         *
         * @param int $customerId
         *
         * @return UserRegisterMetas
         */
        public function setCustomerId($customerId)
        {
            $this->customer_id = $customerId;

            return $this;
        }

        /**
         * Get customer_id
         *
         * @return int
         */
        public function getCustomerId()
        {
            return $this->customer_id;
        }

        /**
         * Set user_ip
         *
         * @param string $userIp
         *
         * @return UserRegisterMetas
         */
        public function setUserIp($userIp)
        {
            $this->user_ip = $userIp;

            return $this;
        }

        /**
         * Get user_ip
         *
         * @return string
         */
        public function getUserIp()
        {
            return $this->user_ip;
        }

        /**
         * Set user_agent
         *
         * @param string $userAgent
         *
         * @return UserRegisterMetas
         */
        public function setUserAgent($userAgent)
        {
            $this->user_agent = $userAgent;

            return $this;
        }

        /**
         * Get user_agent
         *
         * @return string
         */
        public function getUserAgent()
        {
            return $this->user_agent;
        }

        /**
         * Set status
         *
         * @param int $status
         *
         * @return UserRegisterMetas
         */
        public function setStatus($status)
        {
            $this->status = $status;

            return $this;
        }

        /**
         * Get status
         *
         * @return int
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * Set created_at
         *
         * @param date $createdAt
         *
         * @return UserRegisterMetas
         */
        public function setCreatedAt($createdAt)
        {
            $this->created_at = $createdAt;

            return $this;
        }
    }
}
