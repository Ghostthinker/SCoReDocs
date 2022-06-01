<?php

use App\Models\File;
use App\Models\Section;
use App\Models\SectionMedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetSectionIdFromContentToMedia extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $section_medias = SectionMedia::all();
        foreach ($section_medias as $section_media) {
            $section = Section::withTrashed()->find($section_media->section_id);
            if(!$section) {
                print_r("No Section found for section_media: " . $section_media->id . "\n");
                continue;
            }
            $tagArray = explode('>', $section->content);
            foreach ($tagArray as $tag) {
                if($section_media->type !== 'Image') {
                    $mediaId = $this->getMediaIdFromTag($tag, $section_media->ref_id);
                } else {
                    $mediaId = $this->getFileIdFromTag($tag, $section_media->ref_id);
                }
                if($section_media && $mediaId) {
                    print_r("Setting mediable id: " . $mediaId . "to section media with id: " . $section_media->id . "\n");
                    $section_media->mediable_id = $mediaId;
                    $section_media->save();
                    print_r($section_media->mediable_id);
                }
                $mediaId = null;
            }
        }

    }

    private function getMediaIdFromTag($tag, $refId) {
        preg_match('/ref="' . $refId . '"/', $tag, $result);
        if (sizeof($result) > 0) {
            preg_match('/id=\"(\D+\K[\d]*)/', $tag, $result);
            return $result[0];
        }
    }

    private function getFileIdFromTag($tag, $refId) {
        preg_match('/ref="' . $refId . '"/', $tag, $result);
        if (sizeof($result) > 0) {
            preg_match('/src=\"(\K[^"]*)/', $tag, $result);
        }
        $file = null;

        if (sizeof($result) > 0) {
            $file = File::where('path',$result[0])->get()->first();
            if ($file) {
                return $file->id;
            }
        }

        return $file;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('content_to_media', function (Blueprint $table) {
            //
        });
    }
}
