<?php

namespace App\Rules;

class PermissionSet
{
    // === PROJECTS ===

    public const EDIT_PROJECTS = 'edit projects';
    public const CAN_DOWNLOAD_MEDIA = 'can download media';
    public const CAN_DUPLICATE_PROJECT = 'can duplicate project';
    public const CAN_CHANGE_PROJECT_TYPE = 'can change project type';

    // === TEMPLATES ===

    public const EDIT_TEMPLATES = 'edit templates';

    // === ASSESSMENT_DOCS ===

    public const EDIT_ALL_ASSESSMENT_DOCS = 'edit all assessment docs';
    public const CAN_VIEW_ASSESSMENT_DOCS_OVERVIEW = 'can view the assessment docs overview';

    // === ARCHIVED_PROJECTS ===

    public const CAN_VIEW_ARCHIVE = 'can view archive';

    // === SECTIONS ===

    public const CHANGE_HEADING_1_CONTENT = 'change heading 1 content';
    public const CHANGE_HEADING_2_CONTENT = 'change heading 2 content';
    public const SET_HEADING_1_TYPE = 'set heading 1 type';
    public const SET_HEADING_2_TYPE = 'set heading 2 type';
    public const CHANGE_HEADING_1_TYPE = 'change heading 1 type';
    public const CHANGE_HEADING_2_TYPE = 'change heading 2 type';
    public const BREAK_SECTION_WORKFLOW = 'break section workflow';

    public const EDIT_LOCKED_SECTIONS_CONTENT = 'edit locked sections content';
    public const EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW = 'edit sections content with status in review';
    public const EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED = 'edit sections content with status completed';

    public const EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE = 'edit sections heading with status edit not possible';
    public const EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW = 'edit sections heading with status in review';
    public const EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED = 'edit sections heading with status completed';

    public const EDIT_LOCKED_SECTIONS_STATUS = 'edit locked sections status';
    public const SET_LOCKED_SECTIONS_STATUS = 'set locked sections status';
    public const CHANGE_LOCKED_SECTIONS_HEADING = 'change locked sections heading';
    public const CAN_ADD_SECTION_TO_LOCKED_SECTION = 'can add section to locked section';
    public const CAN_ADD_SECTION_WITH_EDIT_NOT_POSSIBLE_STATUS = 'can add section with edit not possible status';

    public const CAN_DELETE_SECTIONS_HEADING_1 = 'can delete sections heading 1';
    public const CAN_DELETE_SECTIONS_HEADING_2 = 'can delete sections heading 2';
    public const CAN_DELETE_SECTIONS_LOCKED = 'can delete sections locked';
    public const CAN_DELETE_SECTIONS_ADVISOR = 'can delete sections advisor';

    public const CAN_ADD_SECTION_TO_ASSESSMENT = 'can add section to assessment';

    // === STATUS ===

    public const SET_STATUS_EDIT_NOT_POSSIBLE = 'set status EditNotPossible';
    public const SET_STATUS_IN_PROGRESS = 'set status InProgress';
    public const SET_STATUS_SUBMITTED = 'set status Submitted';
    public const SET_STATUS_IN_REVIEW = 'set status InReview';
    public const SET_STATUS_COMPLETED = 'set status Completed';

    public const CHANGE_STATUS_EDIT_NOT_POSSIBLE = 'change status EditNotPossible';
    public const CHANGE_STATUS_IN_PROGRESS = 'change status InProgress';
    public const CHANGE_STATUS_SUBMITTED = 'change status Submitted';
    public const CHANGE_STATUS_IN_REVIEW = 'change status InReview';
    public const CHANGE_STATUS_COMPLETED = 'change status Completed';

    // === NEWS ===
    public const CREATE_NEWS = 'create news';
    public const EDIT_NEWS = 'edit news';
    public const DELETE_NEWS = 'delete news';

    // === EP5 ===
    // note: replies are handles as videocomments
    public const UPDATE_OWN_VIDEOCOMMENT = 'update own videocomment';
    public const UPDATE_ANY_VIDEOCOMMENT = 'update any videocomment';
    public const DELETE_OWN_VIDEOCOMMENT = 'delete own videocomment';
    public const DELETE_ANY_VIDEOCOMMENT = 'delete any videocomment';
    public const REPLY_TO_VIDEOCOMMENT = 'reply to videocomment';


    // === Permission Management ===

    public const GET_USERS = 'get users';
    public const SET_ROLES = 'set roles';
    public const GET_ROLES = 'get roles';

    // === Data Export ===
    public const CAN_EXPORT_DATA = 'can export data';

    // === Data Settings ===
    public const CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING = 'can download agreements data processing';

}
