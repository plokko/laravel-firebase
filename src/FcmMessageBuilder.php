<?php
namespace Plokko\LaravelFirebase;


use Plokko\Firebase\ServiceAccount;
use Plokko\Firebase\FCM\{
    Exceptions\UnregisteredException, Message, Request, Targets\Condition, Targets\Target, Targets\Token, Targets\Topic
};

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

    function ttl($ttl){
        $this->message->android->ttl($ttl);
        return $this;
    }


    /**
     * @param Target $target
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException
     */
    function send(Target $target){
        $this->message->setTarget($target);
        try {
            $this->message->send($this->request());
        }catch(UnregisteredException $e){
            if($this->invalidTokenEvent) {
                event($this->invalidTokenEvent,$target);
            }
            throw $e;
        }

    }

    /**
     * @param string $token Device FCM token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException
     */
    function sendToDevice($token){
        $this->send(new Token($token));
    }

    /**
     * @param string $topicName
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException
     */
    function sendToTopic($topicName){
        $this->send(new Topic($topicName));
    }

    /**
     * @param string $condition
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Plokko\Firebase\FCM\Exceptions\FcmErrorException
     */
    function sendToCondition($condition){
        $this->send(new Condition($condition));
    }

}