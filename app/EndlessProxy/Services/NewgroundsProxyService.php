<?php

namespace App\EndlessProxy\Services;

use App\EndlessProxy\Exceptions\SongResolveException;
use App\EndlessProxy\Models\NewgroundsSong;
use App\GeometryDash\Enums\Objects\GeometryDashSongObjectDefinitions;
use App\GeometryDash\Services\GeometryDashObjectService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;

class NewgroundsProxyService
{
	public function __construct(
		protected readonly GeometryDashProxyService  $proxy,
		protected readonly GeometryDashObjectService $object
	)
	{

	}

	/**
	 * @throws SongResolveException
	 */
	public function resolve(int $id): NewgroundsSong
	{
		try {
			$song = NewgroundsSong::query()
				->where('song_id', $id)
				->first();

			$this->resolveSongObjectUsingOfficialServerSongApi($id);

			if (!empty($song)) {
				return $song;
			}

			$songObject = $this->resolveSongObjectFromOfficialServerLevelApi($id);

			if (empty($songObject)) {
				throw new SongResolveException('解析失败, 该歌曲可能不存在');
			}

			return NewgroundsSong::create([
				'song_id' => $songObject[GeometryDashSongObjectDefinitions::ID->value],
				'name' => $songObject[GeometryDashSongObjectDefinitions::NAME->value],
				'artist_id' => $songObject[GeometryDashSongObjectDefinitions::ARTIST_ID->value],
				'artist_name' => $songObject[GeometryDashSongObjectDefinitions::ARTIST_NAME->value],
				'size' => $songObject[GeometryDashSongObjectDefinitions::SIZE->value],
				'disabled' => false,
				'original_download_url' => $songObject[GeometryDashSongObjectDefinitions::DOWNLOAD_URL->value]
			]);
		} catch (ConnectionException $e) {
			throw new SongResolveException('链接异常', previous: $e);
		}
	}

	/**
	 * @throws ConnectionException
	 */
	protected function resolveSongObjectUsingOfficialServerSongApi(int $id): array
	{
		$response = $this->proxy->getRequest()
			->post('getGJSongInfo.php', [
				'songID' => $id,
				'secret' => 'Wmfd2893gb7',
			])
			->body();

		if ($response === '-2') {
			NewgroundsSong::created(function (NewgroundsSong $song) use ($id) {
				if ($song->song_id == $id) {
					$song->update([
						'disabled' => true
					]);
				}
			});
		}

		return $this->object->split($response, GeometryDashSongObjectDefinitions::GLUE);
	}

	/**
	 * @throws ConnectionException
	 */
	protected function resolveSongObjectFromOfficialServerLevelApi(int $id): ?array
	{
		$songApiResult = $this->resolveSongObjectUsingOfficialServerSongApi($id);

		if ($this->validateSongObject($songApiResult)) {
			return $songApiResult;
		}

		$levelApiResult = $this->resolveSongObjectUsingOfficialServerLevelApi($id);

		if ($this->validateSongObject($levelApiResult)) {
			return $levelApiResult;
		}

		return null;
	}

	protected function validateSongObject(array $object): bool
	{
		return Arr::has($object, [
			GeometryDashSongObjectDefinitions::ID->value,
			GeometryDashSongObjectDefinitions::NAME->value,
			GeometryDashSongObjectDefinitions::ARTIST_ID->value,
			GeometryDashSongObjectDefinitions::ARTIST_NAME->value,
			GeometryDashSongObjectDefinitions::SIZE->value,
			GeometryDashSongObjectDefinitions::DOWNLOAD_URL->value,
		]);
	}

	/**
	 * @throws ConnectionException
	 */
	protected function resolveSongObjectUsingOfficialServerLevelApi(int $id): array
	{
		$response = $this->proxy->getRequest()
			->post('getGJLevels21.php', [
				'song' => $id,
				'customSong' => true,
				'secret' => 'Wmfd2893gb7',
			])
			->body();

		return $this->object->split(Arr::get(explode('#', $response), 2), GeometryDashSongObjectDefinitions::GLUE);
	}

	/**
	 * @throws ConnectionException
	 */
	public function toData(NewgroundsSong $song): string
	{
		$url = urldecode($song->original_download_url);

		return $this->proxy->getRequest()
			->get($url)
			->body();
	}

	public function toObject(NewgroundsSong $song): string
	{
		return $this->object->merge([
			GeometryDashSongObjectDefinitions::ID->value => $song->song_id,
			GeometryDashSongObjectDefinitions::NAME->value => $song->name,
			GeometryDashSongObjectDefinitions::ARTIST_ID->value => $song->artist_id,
			GeometryDashSongObjectDefinitions::ARTIST_NAME->value => $song->artist_name,
			GeometryDashSongObjectDefinitions::SIZE->value => $song->size,
			GeometryDashSongObjectDefinitions::DOWNLOAD_URL->value => $song->download_url
		], GeometryDashSongObjectDefinitions::GLUE);
	}
}