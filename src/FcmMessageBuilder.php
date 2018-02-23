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
        $serviceAccount,
        $invalidTokenEvent;

    function __construct(ServiceAccount $serviceAccount)
    {
        $this->serviceAccount = $serviceAccount;
        $this->message = new Message();
    }

    function setInvalidTokenEvent($eventName){
        $this->invalidTokenEvent = $eventName;
    }

    private function request(){
        return new Request($this->serviceAccount);
    }


    function __get($k){
        return $this->message->{$k};
    }


    function data(array $data){
        $this->message->data->fill($data);
        return $this;
    }

    function notificationTitle($title){
        $this->message->notification->setTitle($title);
        return $this;
    }

    function notificationBody($body){
        $this->message->notification->setBody($body);
        return $this;
    }

    /**
     * Set the message priority
     * @param 'high'|"normal" $priority
     * @return $this
     */
    function priority($priority){
        if($priority!=='high' && $priority!=='normal')
            throw new \InvalidArgumentException('Invalid priority value!');
        $this->message->android->setPriority($priority);
        return $this;
    }

    function highPriority(){
        $this->priority('high');
        return $this;
    }

    function normalPriority(){
        $this->priority('normal');
        return $this;
    }

    /**
     * Set the time to live of the message
     * @param string $ttl TTL as a string (ex. '14.5s')
     * @return $this
     */
    function ttl($ttl){
        $this->message->android->ttl($ttl);
        return $this;
    }


    /**
     * Set the message destination,
     * this field is MANDATORY to submit the message
     * @param Target $target
     * @return $this
     */
    function toTarget(Target $target){
        $this->message->setTarget($target);
        return $this;
    }

    /**
     * Set the message destination to a device token
     * @param string $token Target device FCM token
     * @return $this
     */
    function toDevice($token){
        return $this->toTarget(new Token($token));
    }

    /**
     * Set the message destination to a topic name
     * @param string $topicName Target topic name
     * @return $this
     */
    function toTopic($topicName){
        return $this->toTarget(new Topic($topicName));
    }

    /**
     * Set the message destination to a condition
     * @param string $condition Target condition
     * @return $this
     */
    function toCondition($condition){
        return $this->toTarget(new Condition($condition));
    }

    /**
     * Sends the message
     * If no target is specified a FcmTargetNotSpecifiedException will be thrown
     * @throws \GuzzleHttp\Exception\GuzzleException Generic http exception
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException FCMError exception
     * @throws FcmTargetNotSpecifiedException will be thrown if no device target is specified
     */
    function send(){

        if($this->message->token===null){
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