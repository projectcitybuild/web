<?php

namespace Interfaces\Api\Controllers;

use Interfaces\Api\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use Domains\Services\PlayerWarnings\PlayerWarningService;

class GameWarningController extends ApiController
{
    /**
     * @var Validator
     */
    private $validationFactory;

    /**
     * @var PlayerWarningService
     */
    private $playerWarningService;


    public function __construct(PlayerWarningService $playerWarningService,
                                Validator $validationFactory) 
    {
        $this->playerWarningService = $playerWarningService;
        $this->validationFactory = $validationFactory;
    }

    public function storeWarning(Request $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required|max:60',
            'staff_id_type'     => 'required',
            'staff_id'          => 'required|max:60',
            'reason'            => 'string',
            'weight'            => 'integer',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        // TODO: use ServerKey
        $serverId = 1;

        $warnedPlayerId     = $request->get('player_id');
        $staffPlayerId      = $request->get('staff_id');
        $reason             = $request->get('reason');
        $weight             = $request->get('weight');
        
        $warnedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType    = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $warning = $this->playerWarningService->warn($warnedPlayerId,
                                                     $warnedPlayerType->playerType(),
                                                     $staffPlayerId,
                                                     $staffPlayerType->playerType(),
                                                     $reason,
                                                     $serverId,
                                                     $weight);
        // TODO: convert to Resource
        return $warning;
    }

    public function getWarningCount(Request $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required|max:60',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        // TODO: use ServerKey
        $serverId = 1;

        $warnedPlayerId     = $request->get('player_id');
        $warnedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));

        $count = $this->playerWarningService->getWarningCount($warnedPlayerId,
                                                              $warnedPlayerType->playerType(),
                                                              $serverId);
        // TODO: convert to Resource
        return $count;
    }
}
