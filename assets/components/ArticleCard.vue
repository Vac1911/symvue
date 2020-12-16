<template>
  <div class="article">
    <div class="article-cover">
      <img class="w-100" :src="article.cover_photo_url" alt="Not Available">
    </div>
    <div class="px-3 py-2">
      <div class="mb-1">
        <span class="badge bg-secondary mr-1" v-for="tag in article.tags">
          {{tag.name}}
        </span>
      </div>
      <inertia-link class="article-link" :href="article.view_url">{{ article.title }}</inertia-link>
      <div class="article-meta mb-2"><span>By {{ article.author.name }}</span><span>{{ getCreatedAt() }}</span></div>
      <p class="article-text mb-0">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatibus quia, nulla! Maiores et perferendis eaque, exercitationem praesentium nihil.
      </p>
    </div>
    <!--    <div class="px-6 pb-4 flex-grow flex items-end">-->
    <!--      <a class="text-xs" href="{{ route('articles.show', $article) }}">-->
    <!--        More-->
    <!--      </a>-->
    <!--    </div>-->
  </div>
</template>

<script>
  import {DateTime} from "luxon";
  
  export default {
    props: ['article'],
    methods: {
      getCreatedAt() {
        return DateTime.fromISO(this.article.created_at).toRelative();
      }
    }
  }
</script>
