# Development Guide — JobShare Platform

> 本文档同时面向 **AI 编程助手** 和 **人类开发者**。  
> 在开发任何功能前，请务必完整阅读本文件，了解项目约定和规范。

---

## 1. 项目架构总览

```
前端 (Vue 3 + Vite)                   后端 (Laravel 12)
┌─────────────────────┐               ┌──────────────────────┐
│  Views (页面)        │               │  Controllers/Api/    │
│    ↕                │               │    ↕                 │
│  Stores (Pinia)     │  ── Axios ──► │  FormRequests        │
│    ↕                │  ◄── JSON ──  │    ↕                 │
│  API Layer          │               │  Models + Resources  │
└─────────────────────┘               └──────────────────────┘
                                              ↕
                                         MySQL Database

外部 API (前端直接调用，不经过后端):
  • LinkedIn Jobs API → src/api/search.js
  • News API          → src/api/news.js
```

---

## 2. 后端开发规范

### 2.1 目录结构约定

| 目录/文件 | 用途 | 命名规范 |
|-----------|------|----------|
| `app/Http/Controllers/Api/` | API 控制器 | `{资源}Controller.php`，如 `PostController.php` |
| `app/Http/Requests/Api/` | 表单验证 | `{动作}{资源}Request.php`，如 `StorePostRequest.php` |
| `app/Http/Resources/` | API 返回格式化 | `{资源}Resource.php`，如 `PostResource.php` |
| `app/Models/` | Eloquent 模型 | 单数大驼峰，如 `Post.php` |
| `database/migrations/` | 数据库迁移 | Laravel 默认时间戳命名 |
| `routes/api.php` | 所有 API 路由 | 集中定义于此文件 |
| `config/l5-swagger.php` | Swagger 文档配置 | — |

### 2.2 控制器编写规范

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    #[OA\Get(
        path: '/api/posts',
        summary: 'List all posts',
        tags: ['Posts'],
        // ... Swagger 注解
    )]
    public function index()
    {
        $posts = Post::with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }
}
```

**关键规则：**

1. **必须使用 FormRequest 做验证** — 不要在 Controller 里写验证逻辑
2. **必须使用 Resource 格式化输出** — 不要直接 `return $model`
3. **必须添加 OpenApi 注解** — 使用 PHP 8 Attributes 语法（`#[OA\Get(...)]`）
4. **必须用 `with()` 预加载关联** — 避免 N+1 查询问题
5. **统一 JSON 响应格式：**

```json
// 成功 - 单个资源
{ "data": { "id": 1, "title": "..." } }

// 成功 - 集合（分页）
{ "data": [...], "links": {...}, "meta": {...} }

// 成功 - 操作结果
{ "message": "Post deleted successfully" }

// 错误 - 验证失败 (422)
{ "message": "The title field is required.", "errors": { "title": ["..."] } }

// 错误 - 未认证 (401)
{ "message": "Unauthenticated." }
```

### 2.3 认证机制

- 使用 **Laravel Sanctum Token-based 认证**
- 登录成功后返回 `token`，前端存入 `localStorage`
- 需认证的路由放在 `Route::middleware('auth:sanctum')` 组内
- Token 通过 Header 传递：`Authorization: Bearer {token}`

```php
// 登录示例
public function login(LoginRequest $request)
{
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => new UserResource($user),
        'token' => $token,
    ]);
}
```

### 2.4 路由规范

```php
// 公开路由 — 无需认证
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// 认证路由 — 需 Bearer Token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
});
```

**当前已注册的路由（共 16 条）：**

| 分类 | 方法 | 路径 | Controller@method |
|------|------|------|-------------------|
| Auth | POST | `/api/register` | `AuthController@register` |
| Auth | POST | `/api/login` | `AuthController@login` |
| Auth🔒 | POST | `/api/logout` | `AuthController@logout` |
| Auth🔒 | GET | `/api/user` | `AuthController@user` |
| Posts | GET | `/api/posts` | `PostController@index` |
| Posts | GET | `/api/posts/{id}` | `PostController@show` |
| Posts | GET | `/api/search/posts` | `PostController@search` |
| Posts🔒 | POST | `/api/posts` | `PostController@store` |
| Posts🔒 | PUT | `/api/posts/{id}` | `PostController@update` |
| Posts🔒 | DELETE | `/api/posts/{id}` | `PostController@destroy` |
| Posts🔒 | GET | `/api/my-posts` | `PostController@myPosts` |
| Tags | GET | `/api/tags` | `TagController@index` |
| Tags | GET | `/api/tags/{id}` | `TagController@show` |
| Comments | GET | `/api/posts/{postId}/comments` | `CommentController@index` |
| Comments🔒 | POST | `/api/posts/{postId}/comments` | `CommentController@store` |
| Comments🔒 | DELETE | `/api/posts/{postId}/comments/{id}` | `CommentController@destroy` |

> 🔒 = 需要 `auth:sanctum` 中间件

### 2.5 数据模型与关联

```
User ──1:N──► Post ──N:M──► Tag
  │                │
  │                └──1:N──► Comment
  └──1:N──────────────────► Comment
```

| Model | 关联 | 字段 |
|-------|------|------|
| `User` | hasMany(Post), hasMany(Comment) | id, name, email, password |
| `Post` | belongsTo(User), belongsToMany(Tag), hasMany(Comment) | id, user_id, title, content |
| `Tag` | belongsToMany(Post) | id, name |
| `Comment` | belongsTo(Post), belongsTo(User) | id, post_id, user_id, content |

**中间表：** `post_tag` (post_id, tag_id)

### 2.6 Resource 输出格式

已定义好的 Resource 类，**开发时务必使用并保持字段一致**：

```php
// PostResource → 前端接收到的 post 对象结构
{
    "id": 1,
    "title": "My Interview at Google",
    "content": "Here is my experience...",
    "user": { "id": 1, "name": "John", "email": "john@example.com", "created_at": "..." },
    "tags": [{ "id": 1, "name": "interview" }],
    "comments_count": 5,
    "created_at": "2026-03-04T12:00:00Z",
    "updated_at": "2026-03-04T12:00:00Z"
}

// CommentResource
{
    "id": 1,
    "content": "Great post!",
    "user": { "id": 1, "name": "John", "email": "...", "created_at": "..." },
    "created_at": "2026-03-04T12:00:00Z"
}

// TagResource
{
    "id": 1,
    "name": "interview"
}
```

### 2.7 Swagger 文档

- 使用 `darkaonline/l5-swagger` (v10.1)，基于 PHP 8 Attributes
- 基础信息定义在 `app/Http/Controllers/Controller.php`
- Schema 定义在各 Model 上（`#[OA\Schema]`）
- 每个 Controller 方法上都有 `#[OA\Get/Post/Put/Delete]` 注解
- **修改注解后必须执行**：`php artisan l5-swagger:generate`
- **文档地址**：`http://localhost:8000/api/documentation`

---

## 3. 前端开发规范

### 3.1 目录结构约定

```
frontend/src/
├── api/                    # API 请求层（每个资源一个文件）
│   ├── index.js            # Axios 实例 + 拦截器 (不要修改)
│   ├── auth.js             # authApi: register, login, logout, getUser
│   ├── posts.js            # postApi: getAll, getOne, create, update, delete, getMyPosts
│   ├── comments.js         # commentApi: getByPost, create, delete
│   ├── tags.js             # tagApi: getAll, getOne
│   ├── search.js           # searchApi: searchPosts(后端), searchJobs(LinkedIn API)
│   └── news.js             # newsApi: getHeadlines, search (News API 直接调用)
│
├── stores/                 # Pinia 状态管理
│   ├── auth.js             # user, token, isAuthenticated, login, logout...
│   └── post.js             # posts, currentPost, myPosts, loading...
│
├── router/
│   └── index.js            # 路由 + 导航守卫 (requiresAuth / guest)
│
├── views/                  # 页面组件（按功能分目录）
│   ├── HomeView.vue
│   ├── auth/
│   │   ├── LoginView.vue
│   │   └── RegisterView.vue
│   ├── posts/
│   │   ├── PostListView.vue
│   │   ├── PostDetailView.vue
│   │   ├── PostCreateView.vue
│   │   ├── PostEditView.vue
│   │   └── MyPostsView.vue
│   ├── search/
│   │   └── SearchView.vue
│   └── news/
│       └── NewsView.vue
│
├── components/
│   └── layout/
│       ├── AppHeader.vue   # 顶部导航（已完成样式）
│       └── AppFooter.vue   # 页脚（已完成样式）
│
└── assets/
    ├── base.css            # 设计系统 + CSS 变量 (不要修改变量名)
    └── main.css            # 入口样式
```

### 3.2 Vue 组件编写规范

**必须使用 `<script setup>` + Composition API**，不使用 Options API。

```vue
<template>
  <div class="post-list">
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else>
      <div v-for="post in posts" :key="post.id" class="card post-card">
        <h3>{{ post.title }}</h3>
        <p class="text-secondary">{{ post.user.name }}</p>
        <div class="tags">
          <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePostStore } from '@/stores/post'

const postStore = usePostStore()
const { posts, loading } = storeToRefs(postStore)

onMounted(() => {
  postStore.fetchPosts()
})
</script>

<style scoped>
/* 使用 CSS 变量，参考 base.css */
.post-card {
  padding: var(--space-lg);
  margin-bottom: var(--space-md);
}
</style>
```

**关键规则：**

1. **使用 `<script setup>`** — 不要用 `export default { setup() {} }`
2. **使用 scoped style** — `<style scoped>` 避免样式污染
3. **使用 CSS 变量** — 颜色用 `var(--color-*)`, 间距用 `var(--space-*)` 等
4. **使用已有的工具类** — `.card`, `.btn`, `.btn-primary`, `.badge`, `.container` 等
5. **使用 `storeToRefs`** — 从 Store 解构响应式数据时用 `storeToRefs(store)`
6. **路由跳转** — 使用 `useRouter()` 的 `router.push({ name: 'PostDetail', params: { id } })`

### 3.3 API 调用流程

```
View 组件  →  Store (Pinia)  →  API Layer  →  后端 / 外部 API
                                    ↓
                               axios instance (src/api/index.js)
                               自动附加 Bearer Token
                               自动处理 401 跳转登录
```

**调用示例：**

```javascript
// Store 中调用 API
async function fetchPosts(params = {}) {
  loading.value = true
  try {
    const { data } = await postApi.getAll(params)
    posts.value = data.data
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  } finally {
    loading.value = false
  }
}
```

**注意事项：**

- **后端 API** 通过 `src/api/index.js` 统一 Axios 实例调用（自动带 Token）
- **外部 API（LinkedIn、News）** 使用各自独立的 Axios 实例，不走后端代理
- API 响应数据在 `response.data` 中，Laravel 的分页数据在 `response.data.data` 中
- 不要直接在组件中调用 `axios`，必须通过 `src/api/` 下的模块

### 3.4 路由与导航守卫

```javascript
// 已配置的路由元信息：
meta: { requiresAuth: true }  // 需要登录才能访问，未登录跳转 /login
meta: { guest: true }         // 仅未登录可访问（登录/注册页），已登录跳转首页
// 无 meta                    // 公开页面，任何人可访问
```

**添加新路由时：**

```javascript
{
  path: '/new-page',
  name: 'NewPage',                                    // 必须有 name
  component: () => import('@/views/NewPageView.vue'),  // 必须懒加载
  meta: { requiresAuth: true },                        // 按需添加
}
```

### 3.5 设计系统与 CSS 变量

主色调为 Vue 绿色 (#42b883)，整体风格类似 LinkedIn（简洁、卡片式、白底灰背景）。

**可用的 CSS 变量（定义在 `base.css`）：**

```css
/* 颜色 — 必须使用这些变量，不要硬编码颜色值 */
var(--color-primary)          /* #42b883 主色 */
var(--color-primary-light)    /* #5ec89e */
var(--color-primary-lighter)  /* #e8f7f0 浅背景 */
var(--color-primary-dark)     /* #35a472 hover 状态 */
var(--color-surface)          /* #ffffff 卡片背景 */
var(--color-background)       /* #f3f4f6 页面背景 */
var(--color-border)           /* #e5e7eb 边框 */
var(--color-text-primary)     /* #111827 主文字 */
var(--color-text-secondary)   /* #6b7280 次要文字 */
var(--color-text-muted)       /* #9ca3af 辅助文字 */
var(--color-success)          /* #10b981 */
var(--color-warning)          /* #f59e0b */
var(--color-danger)           /* #ef4444 */

/* 间距 */
var(--space-xs)   /* 4px */
var(--space-sm)   /* 8px */
var(--space-md)   /* 16px */
var(--space-lg)   /* 24px */
var(--space-xl)   /* 32px */
var(--space-2xl)  /* 48px */

/* 圆角 */
var(--radius-sm)    /* 4px */
var(--radius-md)    /* 8px */
var(--radius-lg)    /* 12px */
var(--radius-full)  /* 9999px 胶囊形 */

/* 阴影 */
var(--shadow-sm)  /* 轻微阴影 */
var(--shadow-md)  /* 中等阴影 */
var(--shadow-lg)  /* 明显阴影 */
```

**可用的工具类（定义在 `base.css`）：**

| 类名 | 用途 |
|------|------|
| `.container` | 居中容器，max-width: 1128px |
| `.card` | 白色卡片（带边框和阴影） |
| `.btn` | 按钮基础类 |
| `.btn-primary` | 绿色实心按钮 |
| `.btn-outline` | 绿色描边按钮 |
| `.btn-ghost` | 透明按钮 |
| `.btn-sm` / `.btn-lg` | 小号 / 大号按钮 |
| `.badge` | 标签徽章（浅绿背景） |
| `.text-muted` | 灰色辅助文字 |
| `.text-secondary` | 次要文字 |
| `.sr-only` | 仅供屏幕阅读器 |

---

## 4. 功能模块开发检查清单

### 开发一个新的后端接口时：

- [ ] 在 `routes/api.php` 中添加路由，注意放在正确的组（公开 / 认证）
- [ ] 在对应 Controller 中添加方法
- [ ] 如需验证输入，创建 `FormRequest` 类（`php artisan make:request Api/XxxRequest`）
- [ ] 使用 `Resource` 类格式化返回数据
- [ ] 使用 `with()` 预加载关联，避免 N+1
- [ ] 添加 `#[OA\...]` Swagger 注解
- [ ] 执行 `php artisan l5-swagger:generate` 更新文档
- [ ] 在 Swagger UI 中测试接口

### 开发一个新的前端页面时：

- [ ] 在 `src/views/` 下按功能目录创建 `.vue` 文件
- [ ] 在 `src/router/index.js` 中添加路由（懒加载，设定 name 和 meta）
- [ ] 如需调用新接口，在 `src/api/` 对应文件中添加方法
- [ ] 如需共享状态，在 `src/stores/` 对应 Store 中添加 state 和 action
- [ ] 使用 `<script setup>` + Composition API
- [ ] 使用 scoped style + CSS 变量，不要硬编码颜色和间距
- [ ] 复用已有的工具类（`.card`, `.btn`, `.badge` 等）
- [ ] 处理 loading 状态和错误状态
- [ ] 需要认证的操作，路由添加 `meta: { requiresAuth: true }`

---

## 5. 常见开发场景示例

### 5.1 实现帖子列表页

**后端：**

```php
// PostController@index
public function index(Request $request)
{
    $posts = Post::with(['user', 'tags'])
        ->withCount('comments')
        ->latest()
        ->paginate($request->get('per_page', 15));

    return PostResource::collection($posts);
}
```

**前端 Store：**

```javascript
// stores/post.js
async function fetchPosts(params = {}) {
  loading.value = true
  try {
    const { data } = await postApi.getAll(params)
    posts.value = data.data
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  } finally {
    loading.value = false
  }
}
```

**前端页面：**

```vue
<template>
  <div class="post-list">
    <h1>All Posts</h1>
    <div v-if="loading">Loading...</div>
    <template v-else>
      <div v-for="post in posts" :key="post.id" class="card" style="padding: var(--space-lg); margin-bottom: var(--space-md);">
        <h3>
          <router-link :to="{ name: 'PostDetail', params: { id: post.id } }">
            {{ post.title }}
          </router-link>
        </h3>
        <p class="text-secondary">By {{ post.user.name }} · {{ post.comments_count }} comments</p>
        <div>
          <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'

const postStore = usePostStore()
const { posts, loading } = storeToRefs(postStore)

onMounted(() => {
  postStore.fetchPosts()
})
</script>
```

### 5.2 实现创建帖子（含认证）

**后端：**

```php
// PostController@store
public function store(StorePostRequest $request)
{
    $post = $request->user()->posts()->create([
        'title' => $request->title,
        'content' => $request->content,
    ]);

    // 处理 tags — 查找或创建
    $tagIds = collect($request->tags)->map(function ($tagName) {
        return Tag::firstOrCreate(['name' => strtolower(trim($tagName))])->id;
    });
    $post->tags()->sync($tagIds);

    $post->load(['user', 'tags']);

    return (new PostResource($post))
        ->response()
        ->setStatusCode(201);
}
```

**前端：**

```javascript
// stores/post.js
async function createPost(data) {
  const { data: response } = await postApi.create(data)
  return response.data
}
```

```vue
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { usePostStore } from '@/stores/post'

const router = useRouter()
const postStore = usePostStore()

const form = ref({ title: '', content: '', tags: [] })
const errors = ref({})

async function handleSubmit() {
  try {
    errors.value = {}
    const post = await postStore.createPost(form.value)
    router.push({ name: 'PostDetail', params: { id: post.id } })
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    }
  }
}
</script>
```

---

## 6. 环境变量

### 后端 (`backend/.env`)

```dotenv
DB_DATABASE=job_sharing_platform    # MySQL 数据库名
DB_USERNAME=root                     # 数据库用户
DB_PASSWORD=                         # 数据库密码
FRONTEND_URL=http://localhost:5173   # 前端地址（CORS 用）
```

### 前端 (`frontend/.env`)

```dotenv
VITE_API_BASE_URL=http://localhost:8000/api              # 后端 API 地址
VITE_NEWS_API_KEY=                                        # newsapi.org 密钥
VITE_LINKEDIN_JOBS_API_KEY=                               # RapidAPI LinkedIn Jobs 密钥
VITE_LINKEDIN_JOBS_API_URL=https://linkedin-jobs-api.p.rapidapi.com
```

> 前端环境变量必须以 `VITE_` 开头才能在代码中通过 `import.meta.env.VITE_XXX` 访问。

---

## 7. 重要注意事项

### 禁止做的事情 ❌

1. **不要修改 `src/api/index.js` 的拦截器逻辑** — Token 注入和 401 处理已配置好
2. **不要修改 `base.css` 中的 CSS 变量名** — 已被多个组件引用
3. **不要在组件中直接用 `axios`** — 必须通过 `src/api/` 模块
4. **不要在 Controller 中直接 `return Model`** — 必须用 Resource
5. **不要硬编码颜色值** — 使用 CSS 变量 `var(--color-*)`
6. **不要使用 Options API** — 统一使用 `<script setup>` + Composition API
7. **不要在前端存储密码** — 只存 Token（`localStorage.getItem('token')`）
8. **不要跳过 FormRequest** — 每个写操作都需要验证

### 必须做的事情 ✅

1. **每个接口都要有 Swagger 注解** — 改完执行 `php artisan l5-swagger:generate`
2. **每个写操作都要创建 FormRequest** — 验证规则写在 `rules()` 里
3. **关联查询用 `with()` 预加载** — 列表页用 `withCount()` 只查数量
4. **前端错误处理** — 至少处理 `401`（未登录）、`422`（验证错误）、`404`（不存在）
5. **前端 loading 状态** — 每个异步操作都要展示 loading
6. **CSS 使用 scoped** — 防止样式泄漏
7. **路由使用命名导航** — `router.push({ name: 'PostDetail' })` 而非硬编码路径

---

## 8. 常用命令速查

```bash
# 后端
php artisan serve                    # 启动开发服务器 (端口 8000)
php artisan migrate                  # 运行迁移
php artisan migrate:fresh --seed     # 重置数据库并填充种子数据
php artisan make:controller Api/XxxController  # 创建控制器
php artisan make:request Api/XxxRequest        # 创建表单验证
php artisan make:resource XxxResource          # 创建 Resource
php artisan make:model Xxx -m                  # 创建模型 + 迁移
php artisan route:list --path=api              # 查看 API 路由
php artisan l5-swagger:generate                # 生成 Swagger 文档
php artisan tinker                             # 交互式调试

# 前端
npm run dev          # 启动开发服务器 (端口 5173)
npm run build        # 生产构建
npm run preview      # 预览生产构建
```

---

## 9. 团队成员

- **Mei Zhihan** (3036651104)
- **Liu Yinuo** (3036651013)
