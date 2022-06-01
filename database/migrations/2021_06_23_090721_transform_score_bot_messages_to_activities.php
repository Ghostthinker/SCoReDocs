<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransformScoreBotMessagesToActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $scoreBotMessages = \App\Models\Message::where('user_id', 1)->get();
        DB::beginTransaction();
        try {
            foreach ($scoreBotMessages as $message) {
                \App\Models\Activity::create([
                    'user_id' => $message->userId,
                    'type' => 'activity',
                    'message' => $message->data['text'],
                    'section_id' => $message->section_id,
                    'project_id' => $message->project
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return;
        }
        DB::commit();
        \App\Models\MessageReads::whereIn('message_id', $scoreBotMessages->pluck('id')->toArray())->delete();
        \App\Models\Message::whereIn('parent_id',$scoreBotMessages->pluck('id')->toArray())->update(['parent_id' => null]);
        \App\Models\Message::where('user_id', 1)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
}
