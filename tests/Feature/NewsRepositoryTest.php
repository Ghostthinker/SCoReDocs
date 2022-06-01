<?php

namespace Tests\Feature;
use App\Repositories\NewsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic CRUD test
     */
    public function testCRUD()
    {
        $newsRepository = new NewsRepository();
        $news = $newsRepository->create([
            'title' => 'Title',
            'content' => 'Descrption'
        ]);

        $this->assertNotEmpty($news->id);
        $this->assertNotEmpty($news->title);
        $this->assertNotEmpty($news->content);

        //retrieve all
        $all = $newsRepository->all();
        $this->assertCount(1, $all);

        $one = $newsRepository->get($news->id);
        $this->assertEquals($news->id, $one->id);

        $newTitle = ['title' => 'Neuer Title'];

        $status = $newsRepository->update($news->id, $newTitle);
        $this->assertTrue($status);

        $updatedNews = $newsRepository->get($news->id);

        $this->assertEquals($newTitle['title'], $updatedNews->title);

        $newsRepository->delete($news->id);
        $this->assertCount(0, $newsRepository->all());
    }
}
