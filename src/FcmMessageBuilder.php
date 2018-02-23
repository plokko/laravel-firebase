<?php
namespace Plokko\LaravelFirebase;


use Plokko\Firebase\ServiceAccount;
use Plokko\Firebase\FCM\{
    Exceptions\UnregisteredException, Message, Request, Targets\Condition, Targets\Target, Targets\Token, Targets\Topic
};
use Plokko\LaravelFirebase\Exceptions\FcmTargetNotSpecifiedException;

class FcmMessageBuilder
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
    function setInvalidTokenEvent($eventName){
        $this->invalidTokenEvent = $eventName;
        return $this;
    }


    function __get($k){
        return $this->message->{$k};
    }


    /**
     * Set the notification data
     * @param array $data
     * @return $this
     */
    function data(array $data){
        $this->message->data->fill($data);
        return $this;
    }

    /**
     * Set the notification title
     * @param string $title
     * @return $this
     */
    function notificationTitle($title){
        $this->message->notification->setTitle($title);
        return $this;
    }

    /**
     * Set the notification body
     * @param string $body
     * @return $this
     */
    function notificationBody($body){
        $this->message->notification->setBody($body);
        return $this;
    }

    /**
     * Set the message priority
     * @param 'high'|"normal" $priority
     * @return FcmMessageBuilder
     */
    function priority($priority){
        if($priority!=='high' && $priority!=='normal')
            throw new \InvalidArgumentException('Invalid priority value!');
        $this->message->android->setPriority($priority);
        return $this;
    }

    /**
     * Set high priority
     * @return FcmMessageBuilder
     */
    function highPriority(){
        $this->priority('high');
        return $this;
    }

    /**
     * Set normal priority
     * @return FcmMessageBuilder
     */
    function normalPriority(){
        $this->priority('normal');
        return $this;
    }

    /**
     * Set the time to live of the message
     * @param string $ttl TTL as a string (ex. '14.5s')
     * @return FcmMessageBuilder
     */
    function ttl($ttl){
        $this->message->android->ttl($ttl);
        return $this;
    }


    /**
     * Set the message destination,
     * this field is MANDATORY to submit the message
     * @param Target $target
     * @return FcmMessageBuilder
     */
    function toTarget(Target $target){
        $this->message->setTarget($target);
        return $this;
    }

    /**
     * Set the message destination to a device token
     * @param string $token Target device FCM token
     * @return FcmMessageBuilder
     */
    function toDevice($token){
        return $this->toTarget(new Token($token));
    }

    /**
     * Set the message destination to a topic name
     * @param string $topicName Target topic name
     * @return FcmMessageBuilder
     */
    function toTopic($topicName){
        return $this->toTarget(new Topic($topicName));
    }

    /**
     * Set the message destination to a condition
     * @param string $condition Target condition
     * @return FcmMessageBuilder
     */
    function toCondition($condition){
        return $this->toTarget(new Condition($condition));
    }

    /**
     * Get the request for sending
     * @internal
     * @return Request
     */
    private function request(){
        return new Request($this->serviceAccount);
    }

    /**
     * Sends the message
     * If no target is specified a FcmTargetNotSpecifiedException will be thrown
     * @throws \GuzzleHttp\Exception\GuzzleException Generic http exception
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException FCMError exception
     * @throws FcmTargetNotSpecifiedException will be thrown if no device target is specified
     */
    function send(){

        if($this->message->target === null){
            throw new FcmTargetNotSpecifiedException();
        }
        try {
            $this->message->send($this->request());
        }catch(UnregisteredException $e){
            if($this->invalidTokenEvent) {
                event($this->invalidTokenEvent,$this->message->target);
            }
            throw $e;
        }

    }

}