
class Section {

    static create(item) {
        let section = new Section()
        section.id = item.id
        section.title = item.title
        section.oldTitle = item.title
        section.index = item.index
        section.content = item.content ? item.content : ''
        section.oldContent = item.content ? item.content : ''
        section.projectId = item.project_id
        section.heading = item.heading
        section.oldHeading = item.heading
        section.locked = item.locked
        section.username = item.username
        section.lockedAt = item.locked_at
        section.lockedByMe = item.locked_by_me
        section.status = item.status
        section.oldStatus = item.status
        section.statusText = item.statusText
        section.oldStatusText = item.statusText
        section.addSectionPossible = item.addSectionPossible
        section.userIsEntitledToChangeContent = item.entitled_to_change_content
        section.userIsEntitledToChangeHeadingType = item.entitled_to_change_heading_type
        section.userIsEntitledToChangeStatus = item.entitled_to_change_status
        section.userCanDelete = item.user_can_delete
        section.isCollapse = item.isCollapse ? item.isCollapse : 0

        return section
    }
}
export default Section
