<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Company
 *
 * @property int $id
 * @property string $guid
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 */
	class Company extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Endpoint
 *
 * @property int $id
 * @property string|null $company_id
 * @property int $template_id
 * @property string $name
 * @property string $type
 * @property string|null $target
 * @property string|null $filter_terminal
 * @property string|null $filter_zone
 * @property bool $filter_printable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Template $template
 * @method static \Database\Factories\EndpointFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereFilterPrintable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereFilterTerminal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereFilterZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUpdatedAt($value)
 */
	class Endpoint extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $company_id
 * @property int $confirmation_code
 * @property \Spatie\LaravelData\Contracts\BaseData|null $data
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Receipt> $receipts
 * @property-read int|null $receipts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Receipt
 *
 * @property int $id
 * @property int $endpoint_id
 * @property int $order_id
 * @property string|null $status
 * @property array|null $response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Endpoint $endpoint
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt query()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereEndpointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereUpdatedAt($value)
 */
	class Receipt extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Template
 *
 * @property int $id
 * @property string $name
 * @property array $images
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Endpoint> $endpoints
 * @property-read int|null $endpoints_count
 * @method static \Database\Factories\TemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereUpdatedAt($value)
 */
	class Template extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

