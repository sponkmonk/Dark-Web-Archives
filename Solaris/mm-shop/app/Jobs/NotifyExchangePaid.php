<?php

namespace App\Jobs;

use App\Packages\ExchangeAPI\ExchangeAPI;
use App\QiwiExchangeRequest;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyExchangePaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var QiwiExchangeRequest */
    protected $exchangeRequest;

    /**
     * Create a new job instance.
     *
     * @param QiwiExchangeRequest $exchangeRequest
     */
    public function __construct(QiwiExchangeRequest $exchangeRequest)
    {
        $this->exchangeRequest = $exchangeRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExchangeAPI $exchangeAPI)
    {
        $exchangeAPI->setQiwiExchange($this->exchangeRequest->qiwiExchange);

        $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_PAID_REQUEST;
        $this->exchangeRequest->save();

        $result = false;
        try {
            $result = $exchangeAPI->notifyExchange($this->exchangeRequest);
            $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_PAID;
            $this->exchangeRequest->save();
        } catch (RequestException $exception) {
            if ($this->attempts() !== 2) { // try 3 times, then mark as cancelled
                sleep(5);
                throw $exception;
            }
        }

        if (!$result) {
            $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_PAID_PROBLEM;
            $this->exchangeRequest->save();
        }
    }

    public function failed()
    {
        $this->exchangeRequest->error_reason = 'Ошибка соединения с сервером обменника.';
        $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_PAID_PROBLEM;
        $this->exchangeRequest->save();
    }

}
