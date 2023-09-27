<?php
namespace Plokko\LaravelFirebase;

use JsonSerializable;
use Plokko\Firebase\ServiceAccount;
use Plokko\Firebase\FCM\{
    Exceptions\UnregisteredException,
    Message,
    Request,
    Targets\Condition,
    Targets\Target,
    Targets\Token,
    Targets\Topic
};
use Plokko\LaravelFirebase\Exceptions\FcmTargetNotSpecifiedException;
use Illuminate\Contracts\Support\Arrayable;

class FcmMessageBuilder implements JsonSerializable, Arrayable
{
    private
    /**
     * @var ServiceAccount
     */
    $serviceAccount,
    /**
     * @var Message
     */
    $message,
    /**
     * @var string|null
     */
    $invalidTokenEvent;

    function __construct(ServiceAccount $serviceAccount)
    {
        $this->serviceAccount = $serviceAccount;
        $this->message = new Message();
    }

    /**
     * Set the invalid token event (overrides options set in configuration)
     * @param string $eventName
     * @return $this
     */
    function setInvalidTokenEvent($eventName)
    {
        $this->invalidTokenEvent = $eventName;
        return $this;
    }


    function __get($k)
    {
        return $this->message->{$k};
    }


    /**
     * Set the notification data
     * @param array $data
     * @return $this
     */
    function data(array $data)
    {
        $this->message->data->fill($data);
        return $this;
    }

    /**
     * Set the notification title
     * @param string $title
     * @return $this
     */
    function notificationTitle($title)
    {
        $this->message->notification->setTitle($title);
        return $this;
    }

    /**
     * Set the notification sound (Android/IOS);
     * set to "default" for default sound, null to remove
     * @param string $sound Sound name (use 'default' for default sound)
     * @param bool|string $applyApns Specify also in apns payload data for IOS, if true is used $sound will be used, with false apns will not be set otherwise the specified value will be used.
     * @return $this
     */
    function notificationSound($sound, $apns = true)
    {
        $this->message->android->notification->sound = $sound;
        if ($apns !== false) {
            $this->setApnsApsData('sound', $apns === true ? $sound : $apns);
        }
        return $this;
    }

    /**
     * Set the notification body
     * @param string $body
     * @return $this
     */
    function notificationBody($body)
    {
        $this->message->notification->setBody($body);
        return $this;
    }

    /**
     * Set the message priority
     * @param 'high'|"normal" $priority
     * @return $this
     */
    function priority($priority)
    {
        if ($priority !== 'high' && $priority !== 'normal')
            throw new \InvalidArgumentException('Invalid priority value!');
        $this->message->android->setPriority($priority);
        return $this;
    }
    /**
     * Set the message tag (Android/IOS)
     * @param string $tag
     * @return $this
     */
    function notificationTag($tag)
    {
        $this->message->android->notification->tag = $tag;
        return $this;
    }


    /**
     * Set Android message options with a callback
     * @param Closure(\Plokko\Firebase\FCM\Message\AndroidNotification) $closure
     * @return $this
     */
    function setAndroidNotification($closure)
    {
        $closure($this->android->notification);
        return $this;
    }

    /**
     * Set Android message options with a callback
     * @param Closure(\Plokko\Firebase\FCM\Message\AndroidConfig) $closure
     * @return $this
     */
    function setAndroidConfig($closure)
    {
        $closure($this->android);
        return $this;
    }

    /**
     * Set high priority
     * @return $this
     */
    function highPriority()
    {
        $this->priority('high');
        return $this;
    }

    /**
     * Set normal priority
     * @return $this
     */
    function normalPriority()
    {
        $this->priority('normal');
        return $this;
    }

    /**
     * Set the time to live of the message
     * @param string $ttl TTL as a string (ex. '14.5s')
     * @return $this
     */
    function ttl($ttl)
    {
        $this->message->android->ttl($ttl);
        return $this;
    }

    /**
     * Set Aps data in the Apns payload
     * @param string $key Key to set
     * @param mixed $value Value to set
     * @return $this
     */
    function setApnsApsData($key, $value)
    {
        $this->message->apns->payload->aps->$key = $value;
        return $this;
    }

    /**
     * Set Apns payload data
     * @param string $key Key of the payload to set
     * @param mixed $value Value of the payload to set
     * @return $this
     */
    function setApnsPayload($key, $value)
    {
        $this->message->apns->payload->$key = $value;
        return $this;
    }

    /**
     * Get Apns payload data
     * @param string $key Key of the payload to get
     * @return mixed
     */
    function getApnsPayloadValue($key)
    {
        return $this->message->apns->payload->$key;
    }

    /**
     * Set Apns header data
     * @param string $key Key of the header to set
     * @param mixed $value Value of the header to set
     * @return $this
     */
    function setApnsHeader($key, $value)
    {
        $this->message->apns->header->$key = $value;
        return $this;
    }

    /**
     * Get Apns header data
     * @param string $key Key of the header to get
     * @return mixed
     */
    function getApnsHeaderValue($key)
    {
        return $this->message->header->$key;
    }

    /**
     * Set the message destination,
     * this field is MANDATORY to submit the message
     * @param Target $target
     * @return FcmMessageBuilder
     */
    function toTarget(Target $target)
    {
        $this->message->setTarget($target);
        return $this;
    }

    /**
     * Set the message destination to a device token
     * @param string $token Target device FCM token
     * @return FcmMessageBuilder
     */
    function toDevice($token)
    {
        return $this->toTarget(new Token($token));
    }

    /**
     * Set the message destination to a topic name
     * @param string $topicName Target topic name
     * @return FcmMessageBuilder
     */
    function toTopic($topicName)
    {
        return $this->toTarget(new Topic($topicName));
    }

    /**
     * Set the message destination to a condition
     * @param string $condition Target condition
     * @return FcmMessageBuilder
     */
    function toCondition($condition)
    {
        return $this->toTarget(new Condition($condition));
    }

    /**
     * Get the request for sending
     * @internal
     * @return Request
     */
    private function request()
    {
        return new Request($this->serviceAccount);
    }

    /**
     * Sends the message
     * If no target is specified a FcmTargetNotSpecifiedException will be thrown
     * @throws \GuzzleHttp\Exception\GuzzleException Generic http exception
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException FCMError exception
     * @throws FcmTargetNotSpecifiedException will be thrown if no device target is specified
     */
    function send()
    {

        if ($this->message->target === null) {
            throw new FcmTargetNotSpecifiedException();
        }
        try {
            $this->message->send($this->request());
        } catch (UnregisteredException $e) {
            if ($this->invalidTokenEvent) {
                event($this->invalidTokenEvent, $this->message->target);
            }
            throw $e;
        }

    }


    /**
     * Validate the message with Firebase without submitting it
     * @throws \GuzzleHttp\Exception\GuzzleException Generic http exception
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException FCMError exception
     * @throws FcmTargetNotSpecifiedException will be thrown if no device target is specified
     */
    function validate()
    {
        if ($this->message->target === null) {
            throw new FcmTargetNotSpecifiedException();
        }
        try {
            $this->message->validate($this->request());
        } catch (UnregisteredException $e) {
            if ($this->invalidTokenEvent) {
                event($this->invalidTokenEvent, $this->message->target);
            }
            throw $e;
        }

    }

    /**
     * Get FCM message class
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get FCM message payload
     * @return array
     */
    public function getPayload()
    {
        return $this->message->getPayload();
    }

    /**
     * Cast to array
     * @return array
     */
    public function toArray()
    {
        return $this->getPayload();
    }

    /**
     * Cast for JSON serialization
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
    /**
     * Cast to string (as JSON string)
     * @return string
     */
    function __toString()
    {
        return json_encode($this);
    }
}
