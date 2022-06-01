class News {

    static create(item) {
        let news = new News()
        news.id = item.id
        news.title = item.title
        news.content = item.content ? item.content : ''
        news.read = item.read
        news.createdAt = item.created_at
        news.updatedAt = item.updated_at
        news.updatedAtTimestamp = item.updated_at_timestamp
        news.createdAtTimestamp = item.created_at_timestamp

        return news
    }
}
export default News
