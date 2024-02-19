<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\TranslatablePage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Container\Container;

class BlockPagesSeeder extends Seeder
{

    protected $pages =  [
            'TEXT_BLOCK_PAGE' => [
                'content' => [
                    'title' => [
                        'en' => 'Text block',
                        'nl' => 'Tekstblok',
                    ],
                    'intro' => [
                        'en' => "A basic block with title and text.",
                        'nl' => 'Een eenvoudig blok met titel en tekst.',
                    ],
                    'slug' => [
                        'en' => 'text-block-page',
                        'nl' => 'tekstblok-pagina',
                    ],
                ],
                'blocks' => [
                    [
                        'block_type' => 'text',
                        'block_style' => 'default',
                        'background_colour' => 'primary'
                    ]
                ]
            ],
            'TEXT_IMAGE_BLOCK_PAGE' => [
                'content' => [
                    'title' => [
                        'en' => 'Text-image block',
                        'nl' => 'Tekst-afbeeldingblok',
                    ],
                    'intro' => [
                        'en' => "A basic block with title, image and text.",
                        'nl' => 'Een eenvoudig blok met titel, afbeelding en tekst.',
                    ],
                    'slug' => [
                        'en' => 'text-image-block-page',
                        'nl' => 'tekst-afbeelding-blok-pagina',
                    ],
                ],
                'blocks' => [
                    [
                        'block_type' => 'text-image',
                        'block_style' => 'default',
                        'background_colour' => 'primary'
                    ]
                ],
            ],
            'IMAGE_BLOCK_PAGE' => [
                'content' => [
                    'title' => [
                        'en' => 'Image block',
                        'nl' => 'Afbeeldingblok',
                    ],
                    'intro' => [
                        'en' => "A basic block with title and image.",
                        'nl' => 'Een eenvoudig blok met titel en afbeelding',
                    ],
                    'slug' => [
                        'en' => 'image-block-page',
                        'nl' => 'afbeelding-blok-pagina',
                    ],
                ],
                'blocks' => [
                    [
                        'block_type' => 'image',
                        'block_style' => 'default',
                        'background_colour' => 'primary'
                    ]
                ],
            ],
            'CARDS_PAGE' => [
                'content' => [
                    'title' => [
                        'en' => 'Cards block',
                        'nl' => 'Kaartenblok',
                    ],
                    'intro' => [
                        'en' => "This block is comparable to the overview block, however you can add the title, description, image and CTA for each card. The image conversion, background colour and grid columns can be configured.",
                        'nl' => 'Dit blok is vergelijkbaar met het overzichtsblok, maar je kan de titel, beschrijving, afbeelding en call to action instellen voor iedere kaart afzonderlijk. De afbeeldingsconversie, achtergrondkleur en rasterkolommen kunnen ook ingesteld worden.',
                    ],
                    'slug' => [
                        'en' => 'cards-block-page',
                        'nl' => 'kaarten-blok-pagina',
                    ],
                ],
                'blocks' => [
                    [
                        'block_type' => 'cards',
                        'block_style' => 'default',
                        'background_colour' => 'primary'
                    ]
                ],
            ],
            'VIDEO_PAGE' => [
                'content' => [
                    'title' => [
                        'en' => 'Video block',
                        'nl' => 'Videoblok',
                    ],
                    'intro' => [
                        'en' => "You can embed videos from numerous media services and set an overlay image that will cause the video embed to be lazy loaded after clicking the image.",
                        'nl' => "Je kan video's van meerdere media services embedden en een overlay afbeelding instellen die ervoor zorgt dat de video pas geladen wordt nadat de afbeelding aangeklikt wordt.",
                    ],
                    'slug' => [
                        'en' => 'video-block-page',
                        'nl' => 'video-blok-pagina',
                    ],
                ],
                'blocks' => [
                    [
                        'block_type' => 'video',
                    ]
                ],
            ],

        ];


    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = $this->pages;
        foreach($pages as $code => $page_data){
            // create empty content_blocks
            $page_data['content']['content_blocks'] = ['en' => [], 'nl' => []];
            $page_data['content']['hero_image_copyright'] = ['en' => NULL, 'nl' => NULL]; // Necessary because Field 'hero_image_copyright' doesn't have a default value
            $page_data['content']['hero_image_title'] = ['en' => NULL, 'nl' => NULL]; // Necessary because Field 'hero_image_title' doesn't have a default value
            $page_data_en = array_combine(array_keys($page_data['content']), array_column($page_data['content'],'en'));

            //TODO: 1 model per seeder

            // make translatable page
            $translatable_page = TranslatablePage::updateOrCreate(['code'=> $code], $page_data['content']);
            $langcodes = ['en', 'nl'];
            foreach($langcodes as $langcode){
                // add seo
                $translatable_page->addMedia($this->faker->image(public_path(),400,300, category:null, fullPath:true))->withCustomProperties(['locale' => $langcode])->toMediaCollection("seo_image");
                $page_data['content']['seo_title'][$langcode] = $this->faker->sentence();
                $page_data['content']['seo_description'][$langcode] = $this->faker->paragraph();
                $page_data['content']['seo_keywords'][$langcode] = [$this->faker->word(), $this->faker->word(), $this->faker->word()];
                // add hero image
                $translatable_page->addMedia($this->faker->image(public_path(),400,300, category:null, fullPath:true))->withCustomProperties(['locale' => $langcode])->toMediaCollection("hero_image");
                $page_data['content']['hero_image_title'][$langcode] = $this->faker->sentence();
                $page_data['content']['hero_image_copyright'][$langcode] = $this->faker->sentence();
                // add overview
                $page_data['content']['overview_title'][$langcode] = $this->faker->sentence();
                $page_data['content']['overview_description'][$langcode] = $this->faker->paragraph();
                foreach($page_data['blocks'] as $block){
                    $page_data['content']['content_blocks'][$langcode][] = $this->makeBlockOfType($block, $translatable_page);
                }
            }
            TranslatablePage::updateOrCreate(['code'=> $code], $page_data['content']);

            // make simple page
            $page = Page::updateOrCreate(['code'=> $code], $page_data_en);
            // add seo
            $page->addMedia($this->faker->image(public_path(),400,300, category:null, fullPath:true))->toMediaCollection("seo_image");
            $page_data_en['content']['seo_title'] = $this->faker->sentence();
            $page_data_en['content']['seo_description'] = $this->faker->paragraph();
            $page_data_en['content']['seo_keywords'] = [$this->faker->word(), $this->faker->word(), $this->faker->word()];
            // add hero image
            $page->addMedia($this->faker->image(public_path(),400,300, category:null, fullPath:true))->toMediaCollection("hero_image");
            $page_data_en['content']['hero_image_title'] = $this->faker->sentence();
            $page_data_en['content']['hero_image_copyright'] = $this->faker->sentence();
            // add overview
            $page_data_en['content']['overview_title'] = $this->faker->sentence();
            $page_data_en['content']['overview_description'] = $this->faker->paragraph();
            foreach($page_data['blocks'] as $block){
                $page_data_en['content']['content_blocks'][] = $this->makeBlockOfType($block, $page);
            }
            Page::updateOrCreate(['code'=> $code], $page_data_en['content']);
        }
    }

    private function makeBlockOfType($block, $page){
        $type = $block['block_type'];
        unset($block['block_type']);
        switch($type) {
            case 'text':
                $block = $this->createTextBlock($page, ...$block);
                break;
            case 'video':
                $block = $this->createVideoBlock($page);
                break;
            case 'image':
                $block = $this->createImageBlock($page, ...$block);
                break;
            case 'html':
                $block = $this->createHtmlBlock($page, ...$block);
                break;
            case 'text-image':
                $block = $this->createTextImageBlock($page, ...$block);
                break;
            case 'overview':
                $block = $this->createOverviewBlock($page, ...$block);
                break;
            case 'quote':
                $block = $this->createQuoteBlock($page, ...$block);
                break;
            case 'call-to-action':
                $block = $this->createCallToActionBlock($page, ...$block);
                break;
            case 'cards':
                $block = $this->createCardsBlock($page, ...$block);
                break;
            case 'template':
                $block = $this->createTemplateBlock($page, ...$block);
                break;
        }
        return $block;
    }

    private function createTextBlock($page, $block_style='default', $background_colour='primary'){
        return [
            "data" => [
                "title" => $this->faker->sentence(),
                "content" => $this->faker->paragraph(),
                "block_style" => $block_style,
                "background_colour" => $background_colour,
            ],
            "type" => "filament-flexible-content-blocks::text",
        ];
    }

    private function createVideoBlock($page) {
        $image = $this->faker->image(public_path(),400,300, category:null, fullPath:true);
        $mediaObject = $page->addMedia($image)->toMediaCollection("filament-flexible-content-blocks::video");
        return [
            "data" => [
                "overlay_image" => $mediaObject->uuid ,
                "embed_url" => "https://www.youtube.com/watch?v=mw4k1tCnAuE", // TODO: uitzoeken hoe random video url genereren (of iets beters dan dit, evt zelf online zetten? Vragen aan iemand?) + video speelt niet: thema?
            ],
            "type" => "filament-flexible-content-blocks::video",
        ];
    }

    private function createImageBlock($page, $block_style='default', $background_colour='primary') {
        $image = $this->faker->image(public_path(),400,300, category:null, fullPath:true);
        $mediaObject = $page->addMedia($image)->toMediaCollection("filament-flexible-content-blocks::image");
        return [
            "data" => [
                "image" => $mediaObject->uuid ,
                "image_title" => $this->faker->sentence(),
                "image_width" => "50%",
                "image_position" => "center",
                "image_copyright" => $this->faker->sentence(),
                "image_conversion" => "contain",
                "block_style" => $block_style,
                "background_colour" => $background_colour,
            ],
            "type" => "filament-flexible-content-blocks::image",
        ];
    }

    private function createHtmlBlock($page, $block_style='default', $background_colour='primary') {
            return [
            "data" => [

            ],
            "type" => "filament-flexible-content-blocks::html",
        ];
    }

    private function createTextImageBlock($page, $block_style='default', $background_colour='primary') {
        $image = $this->faker->image(public_path(),400,300, category:null, fullPath:true);
        $mediaObject = $page->addMedia($image)->toMediaCollection("filament-flexible-content-blocks::text-image");
        return [
            "data" => [
                "title" => $this->faker->sentence(),
                "text" => $this->faker->paragraph(),
                "image" => $mediaObject->uuid ,
                "image_title" => $this->faker->sentence(),
                "image_width" => "50%",
                "image_position" => "center",
                "image_copyright" => $this->faker->sentence(),
                "image_conversion" => "contain",
                "block_style" => $block_style,
                "background_colour" => $background_colour,
                //TODO: call to action ??
            ],
            "type" => "filament-flexible-content-blocks::text-image",
        ];
    }

    private function createOverviewBlock($page, $block_style='default', $background_colour='primary') {
            return [
            "data" => [

            ],
            "type" => "filament-flexible-content-blocks::overview",
        ];
    }

    private function createQuoteBlock($page, $block_style='default', $background_colour='primary') {
            return [
            "data" => [

            ],
            "type" => "filament-flexible-content-blocks::quote",
        ];
    }

    private function createCallToActionBlock($page, $block_style='default', $background_colour='primary') {
            return [
            "data" => [

            ],
            "type" => "filament-flexible-content-blocks::call-to-action",
        ];
    }

    private function createCardsBlock($page, $block_style='default', $background_colour='primary') {
        $cards = [
            "data" => [
                "title" => $this->faker->sentence(),
                "grid_columns" => 3,
                "image_conversion" => "crop",
                "block_style" => $block_style,
                "background_colour" => $background_colour,
                "cards" => [],
            ],
            "type" => "filament-flexible-content-blocks::cards",
        ];
        for($i=0; $i<10; $i++){
            $image = $this->faker->image(public_path(),400,300, category:null, fullPath:true);
            $mediaObject = $page->addMedia($image)->toMediaCollection("filament-flexible-content-blocks::cards");
            $cards["data"]["cards"][] = [
                "title" =>  $this->faker->sentence(),
                "text" =>  $this->faker->paragraph(),
                "image" => $mediaObject->uuid ,
                "image_title" => $this->faker->sentence(),
                "image_width" => "50%",
                "image_position" => "center",
                "image_copyright" => $this->faker->sentence(),
                "image_conversion" => "contain",
                "card_call_to_action" => [],
            ];
        }
        return $cards;
    }

    private function createTemplateBlock($page, $block_style='default', $background_colour='primary') {
        return [
            "data" => [

            ],
            "type" => "filament-flexible-content-blocks::template",
        ];
    }



}

