
class User {

    static create(item) {
        let user = new User()
        user.id = item.id
        user.name = item.name
        user.assessmentDocId = item.assessment_doc_id
        user.meta = {
            hasSeenIntroVideo : item.meta.hasSeenIntroVideo,
            canAccessUserAdministration : item.meta.canAccessUserAdministration,
            canAccessAssessmentOverview : item.meta.canAccessAssessmentOverview,
            canAccessTemplate: item.meta.canAccessTemplate,
            canAccessDataExport: item.meta.canAccessDataExport,
            canAccessAssessmentDoc: item.meta.canAccessAssessmentDoc,
            canAccessDownloadAgreementDataProcessing: item.meta.canAccessDownloadAgreementDataProcessing,
            leftMenuCollapsed: item.meta.leftMenuCollapsed ? item.meta.leftMenuCollapsed : 0,
            rightMenuCollapsed: item.meta.rightMenuCollapsed ? item.meta.rightMenuCollapsed : 0,
        }
        return user
    }
}
export default User
