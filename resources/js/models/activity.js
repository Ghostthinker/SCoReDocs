
class Activity {

    static create(item) {
        let activity = new Activity()
        activity.id = item.id
        activity.isSectionDeleted = item.isSectionDeleted
        activity.message = item.message
        activity.projectId = item.projectId
        activity.projectTitle = item.projectTitle
        activity.sectionId = item.sectionId
        activity.sectionTitle = item.sectionTitle
        activity.type = item.type
        activity.userId = item.userId
        activity.read = item.read
        activity.targetUserIds = item.targetUserIds
        return activity
    }
}
export default Activity
