class Media {

    static create(item) {
        let media = new Media()
        media.id = item.id
        media.status = parseInt(item.status)
        media.fileName = item.fileName
        media.caption = item.caption
        media.previewURL = item.previewURL
        media.streamingId = item.streamingId
        media.streamingURL_720p = item.streamingURL_720p
        media.streamingURL_1080p = item.streamingURL_1080p
        media.streamingURL_2160p = item.streamingURL_2160p
        media.createdAt = item.created_at
        media.updatedAt = item.updated_at
        return media
    }
}
export const MediaTypes = Object.freeze({
    'CREATED' : 0,
    'PENDING' : 1,
    'CONVERTED' : 2,
    'FAILED_CONVERT' : 3,
    'FAILED_UPLOAD' : 4,
})

export default Media