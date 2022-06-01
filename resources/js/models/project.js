class Project {
    static create(item) {
        let project = new Project()
        project.id = item.id,
        project.title = item.title,
        project.description = item.description,
        project.type = item.type,
        project.isUserWatchingProject = item.is_user_watching_project,
        project.basicCourse = item.basic_course
        return project
    }
}
export default Project
