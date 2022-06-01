<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

/*
**************************
 *  HTML ROUTES
**************************
*/
Route::get('/imprint', function () {
    return view('pages.imprint');
})->name('imprint');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('files/{file_name}', function($file_name = null)
{
    $path = storage_path().'/'.'app'.'/files/'.$file_name;
    if (file_exists($path)) {
        return Response::download($path);
    }
});

Route::get('files/{file_name}/deliver', function($file_name = null)
{
    $path = storage_path().'/'.'app'.'/files/'.$file_name;
    if (file_exists($path)) {
        return Response::file($path);
    }
});

Route::get('/bug_attachments/{file_name}', function (Request $request, $file_name = null) {
    if (! $request->hasValidSignature()) {
        abort(401);
    }
    $path = storage_path().'/'.'app'.'/bug_attachments/'.$file_name;
    if (file_exists($path)) {
        return Response::download($path);
    }
})->name('bug_attachments');


/*
**************************
 *  JSON ROUTES
**************************
*/

Route::post('mail/support', 'MailController@sendSupportRequest');

Route::namespace('EP5')->prefix('rest')->middleware('auth')->group(function () {

    //annotation
    Route::get('ep5/comments/{media_id}', 'AnnotationController@index');
    Route::post('ep5/comments/{media_id}', 'AnnotationController@store');
    Route::put('ep5/comments/{media_id}/{annotation_id}', 'AnnotationController@update');
    Route::delete('ep5/comments/{media_id}/{annotation_id}', 'AnnotationController@destroy');

    //reply
    Route::post('ep5/comments/{media_id}/reply/{parent_id}', 'AnnotationController@addReply');
    Route::put('ep5/comments/{media_id}/reply/{parent_id}/{reply_id}', 'AnnotationController@updateReply');
    Route::delete('ep5/comments/{media_id}/reply/{parent_id}/{reply_id}', 'AnnotationController@destroyReply');

    // playbackCommand
    Route::get('ep5/playbackcommands/{media_id}', 'PlaybackCommandController@index');
    Route::post('ep5/playbackcommands/{media_id}', 'PlaybackCommandController@store');
    Route::put('ep5/playbackcommands/{media_id}/{playbackcommand_id}', 'PlaybackCommandController@update');
    Route::delete('ep5/playbackcommands/{media_id}/{playbackcommand_id}', 'PlaybackCommandController@destroy');

    // sequences
    Route::get('ep5/sequences/{media_id}', 'VideoSequenceController@index');
    Route::get('ep5/sequences/{media_id}/sequence/{sequence_id}', 'VideoSequenceController@show');
    Route::post('ep5/sequences/{media_id}', 'VideoSequenceController@store');
    Route::put('ep5/sequences/{media_id}/{sequences_id}', 'VideoSequenceController@update');
    Route::delete('ep5/sequences/{media_id}/{sequences_id}', 'VideoSequenceController@destroy');
    Route::get('ep5/sequences/{media_id}/sequence/{sequences_id}/download', 'VideoSequenceController@download');
    Route::get('ep5/sequences/{media_id}/sequence/{sequences_id}/download/progress', 'VideoSequenceController@progress');
});

Route::middleware('auth')->group(function () {
    Route::get('rest/section/{section_id}/playlist', 'SectionController@getPlaylist');
    Route::get('rest/section/{section_id}/playlist/download', 'SectionController@downloadPlaylist');
    Route::get('rest/section/{section_id}/playlist/download/progress/{playlist_id}', 'SectionController@getPlaylistProgress');
    Route::get('rest/section/{section_id}/playlist/download/{playlist_id}', 'SectionController@getPlaylistDownloadUrl');
    Route::resource('rest/media', 'MediaController');
    Route::get('rest/media/{media_id}', 'MediaController@getByMediaId');

    //download
    Route::get('rest/media/{media_id}/download', 'MediaController@download');
    Route::get('rest/media/{media_id}/download/uncompressed', 'MediaController@downloadUncompressed');

    Route::get('/media', function () {
        return view('pages.media');
    });
    Route::view('/media/{catchall?}', 'pages.media')->where('catchall', '(.*)');

    Route::middleware('role:Admin')->group(function () {
        Route::group(['prefix' => 'rest/users'], function () {
            Route::get('', 'UserController@getUsers');
            Route::get('roles', 'UserController@getRoles');
            Route::patch('roles', 'UserController@updateRoles');
        });
    });

    Route::get('rest/user', 'UserController@getCurrentUser');
    Route::get('rest/user/markIntroVideoAsSeen', 'UserController@markIntroVideoAsSeen');
    Route::get('rest/user/toggleMenuCollapseState/{menu}', 'UserController@setMenuCollapseState');

    Route::post('rest/message/send/{project_id}/{section_id?}', 'ChatController@sendMessage');
    Route::get('rest/messages/get/{project_id}', 'ChatController@getMessagesForProject');
    Route::get('rest/messages/get/{project_id}/{section_id}', 'ChatController@getMessagesForContext');
    Route::get('rest/messages/participants/project/{project}', 'ChatController@getParticipants');
    Route::get('rest/messages/unreadMessageCounts/{project_id}', 'ChatController@getUnreadMessageCounts');
    Route::get('rest/messages/updateReadMessages/{project_id}/{section_id}', 'ChatController@updateReadMessages');
    Route::get('rest/messages/mentioning/{message_mentioning_id}/markAsRead', 'ChatController@markMessageMentioningAsRead');
    Route::get('rest/project/{project_id}/mentionings', 'ChatController@getMessageMentioningsForProject');
    Route::get('rest/messages/{project_id}/markAllSectionMessagesAsRead', 'ChatController@markAllSectionMessagesAsRead');


    Route::get('rest/profile', 'ProfileController@show');
    Route::patch('rest/profile', 'ProfileController@update');

    // Projects
    Route::get('rest/projects', 'ProjectController@getProjects');
    Route::get('rest/project/{project_id}', 'ProjectController@getProject');
    Route::get('rest/project/{project}/duplicate', 'ProjectController@duplicateProject')
        ->middleware('can:can duplicate project');
    Route::get('rest/project/{project}/toggleWatch', 'ProjectController@toggleWatchProject');

    Route::middleware('can:edit projects')->group(function () {
        Route::post('rest/project', 'ProjectController@store');
        Route::put('rest/project/{project_id}', 'ProjectController@update');
        Route::delete('rest/project/{project_id}', 'ProjectController@destroy');
    });

    // News
    Route::get('rest/news', 'NewsController@index');
    Route::post('rest/news/{news_id}/read', 'NewsController@read');
    Route::middleware('can:create news')->group(function () {
        Route::post('rest/news', 'NewsController@store');
    });
    Route::middleware('can:edit news')->group(function () {
        Route::put('rest/news', 'NewsController@update');
    });
    Route::middleware('can:delete news')->group(function () {
        Route::delete('rest/news/{news_id}', 'NewsController@delete');
    });

    // Templates
    Route::middleware('permission:edit templates')->group(function () {
        Route::group(['prefix' => 'rest/templates'], function () {
            Route::get('getAssessmentDocTemplate', 'ProjectTemplateController@getAssessmentDocTemplate');
            Route::get('getProjectTemplates', 'ProjectTemplateController@getProjectTemplates');
            Route::post('', 'ProjectTemplateController@store');
        });
    });

    // Assessment Docs
    Route::middleware('can:can view the assessment docs overview')->group(function () {
        Route::get('rest/assessment-overview-data', 'ProjectAssessmentController@getAssessmentDocs');
    });

    // Archive
    Route::get('rest/archivedProjects', 'ProjectArchiveController@getArchivedProjects');

    // Data Export
    Route::middleware('can:can export data')->group(function () {
        Route::get('rest/data/download', 'DataExportController@download');
        Route::get('rest/data/last', 'DataExportController@lastDataExport');
    });

    Route::get('/download-agreements-data-processing', 'UserController@downloadAgreementsDataProcessing')->name('download-agreements-data-processing');

    Route::group(['prefix' => 'rest/projects/{project_id}/sections'], function () {
        Route::post('', 'SectionController@store');
        Route::get('', 'SectionController@getSections');
        Route::get('{section_id}', 'SectionController@getSection');
        Route::get('{section_id}/trashed', 'SectionController@getDeletedSection');
        Route::get('deleted/all', 'SectionController@getDeletedSections');
        Route::get('{section_id}/revert/{top_section_id}', 'SectionController@revertSection');
        Route::get('{section_id}/status', 'SectionController@getPossibleSectionStatus');
        Route::get('{section}/audits', 'SectionController@getAudits');
        Route::put('{section_id}', 'SectionController@update');
        Route::patch('{section_id}/timeout/reset', 'TimeoutController@resetTimeout');
        Route::patch('{section_id}/lock', 'SectionController@lock');
        Route::patch('{section_id}/unlock', 'SectionController@unlock');
        Route::delete('{section_id}', 'SectionController@destroy');
        Route::post('{section_id}/link', 'LinkController@store');
        Route::get('{section}/open', 'SectionController@openSection');
        Route::get('{section}/close', 'SectionController@closeSection');
    });

    Route::get('rest/refs/{ref_id}/count', 'LinkController@getCount');
    Route::get('rest/media/{media_id}/annotations/count', 'EP5\AnnotationController@getCount');
    Route::get('/rest/media/{media_id}/sequence/{sequence_id}/count', 'EP5\AnnotationController@getSequenceCount');

    // Upload
    Route::get('file/uploads/{filename}', 'FileController@deliver')->name('file.deliver');
    Route::resource('rest/file', 'FileController');

    // xAPI
    Route::group(['prefix' => 'rest/xapi'], function () {
        Route::get('projects/{project}/leftproject', 'XapiController@leftProject');
        Route::get('projects/{project}/openproject', 'XapiController@openProject');
        Route::get('annotation/{annotation}/insert', 'XapiController@insertedAnnotation');
        Route::get('activities/{activity}/clicked', 'XapiController@clickedActivity');

        Route::group(['prefix' => 'projects/{project}/sections/{section}'], function () {
            Route::get('startedediting', 'XapiController@startedEditing');
            Route::get('canceledediting', 'XapiController@canceledEditing');
            Route::get('viewedhistory', 'XapiController@viewedHistory');
            Route::get('viewed', 'XapiController@viewed');
            Route::get('revertedversion/{version}', 'XapiController@revertedVersion');
            Route::get('comparedversions/{version_one}/{version_two}', 'XapiController@comparedVersions');

            Route::post('videos/{media}', 'XapiMediaController@index');
        });

        Route::post('projects/{project}/chat', 'XapiChatController@index');
        Route::post('media/{media}', 'XapiMediaController@only');
        Route::get('project/{project}/markAllAsRead', 'XapiController@markAllAsRead');
    });

    // activity
    Route::get('rest/activities', 'ActivityController@getActivitiesForUser');
    Route::get('rest/activities/project/{project_id}', 'ActivityController@getActivitiesForProjectId');
    Route::get('rest/activities/project/{project_id}/section/{section_id}', 'ActivityController@getActivitiesForProjectIdBySectionId');
    Route::get('rest/activity/{activity}/markAsRead', 'ActivityController@markAsRead');
    Route::get('rest/activity/unreadActivitiesCount/{project_id}', 'ActivityController@getUnreadActivitiesCount');
    Route::get('rest/activity/markAllActivitiesAsRead/{project_id}', 'ActivityController@markAllActivitiesAsRead');

    // mailLink
    Route::get('/xapi/link/{type}', 'XapiController@clickedMailLink');

    Route::get('/{any}', function () {
        return view('pages.projects');
    })->where('any', '.*');
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
