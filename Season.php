<? 
/**
 * Takes a date/timestamp and turns it into a season you can iterate on or return a range of seasons
 *
 * Usage:
 *
 * // initialize a new iterator by passing in the date of the season you want to start at
 * $season = new Season('now');
 * 
 * // now you could return an array of seasons by passing in an end date to getRange(),
 * $seasons = $season->getRange('+2 years');
 * 
 * // or use the iterator functions, which will each move the season pointer accordingly
 * echo $season->current();   // returns current season
 * echo $season->next();      // returns next season
 * echo $season->previous();  // returns previous season
 *
 * @copyright  2010, iMarc LLC <info@imarc.net>
 * @author	   Craig Ruks [cr] <craigruks@imarc.net>
 * 
 * @requires   fTimestamp class and dependencies http://flourishlib.com/docs/AdvancedDownload
 *
 * @version    1.0.0
 */
class Season implements Iterator
{
	/**
	 * The current season being iterated upon
	 * @var string
	 */
	var $index;

	/**
	 * The original season the iterator began with
	 * @var string
	 */
	var $orignal_index;

	/**
	 * An array of fTimestamp objects of every season upon construction of an instance.
	 * 
	 * @var array
	 */
	static private $season_changes = array();

	/**
	 * An array of every season (in RFC 2822 format) within the PHP range of time(). This seeds an array of fTimestamp objects upon construction of the class.
	 * 
	 * @var array
	 */
	static public $season_change_strings = array(
		'Sun, 22 Dec 1901 05:00:00 +0000 UTC',
		'Fri, 21 Mar 1902 05:00:00 +0000 UTC',
		'Sun, 22 Jun 1902 05:00:00 +0000 UTC',
		'Tue, 23 Sep 1902 05:00:00 +0000 UTC',
		'Mon, 22 Dec 1902 05:00:00 +0000 UTC',
		'Sat, 21 Mar 1903 05:00:00 +0000 UTC',
		'Mon, 22 Jun 1903 05:00:00 +0000 UTC',
		'Wed, 23 Sep 1903 05:00:00 +0000 UTC',
		'Tue, 22 Dec 1903 05:00:00 +0000 UTC',
		'Sun, 20 Mar 1904 05:00:00 +0000 UTC',
		'Tue, 21 Jun 1904 05:00:00 +0000 UTC',
		'Fri, 23 Sep 1904 05:00:00 +0000 UTC',
		'Wed, 21 Dec 1904 05:00:00 +0000 UTC',
		'Mon, 20 Mar 1905 05:00:00 +0000 UTC',
		'Wed, 21 Jun 1905 05:00:00 +0000 UTC',
		'Sat, 23 Sep 1905 05:00:00 +0000 UTC',
		'Fri, 22 Dec 1905 05:00:00 +0000 UTC',
		'Wed, 21 Mar 1906 05:00:00 +0000 UTC',
		'Thu, 21 Jun 1906 05:00:00 +0000 UTC',
		'Sun, 23 Sep 1906 05:00:00 +0000 UTC',
		'Sat, 22 Dec 1906 05:00:00 +0000 UTC',
		'Thu, 21 Mar 1907 05:00:00 +0000 UTC',
		'Sat, 22 Jun 1907 05:00:00 +0000 UTC',
		'Mon, 23 Sep 1907 05:00:00 +0000 UTC',
		'Sun, 22 Dec 1907 05:00:00 +0000 UTC',
		'Fri, 20 Mar 1908 05:00:00 +0000 UTC',
		'Sun, 21 Jun 1908 05:00:00 +0000 UTC',
		'Wed, 23 Sep 1908 05:00:00 +0000 UTC',
		'Mon, 21 Dec 1908 05:00:00 +0000 UTC',
		'Sun, 21 Mar 1909 01:13:43 +0000 UTC',
		'Mon, 21 Jun 1909 22:06:12 +0000 UTC',
		'Thu, 23 Sep 1909 12:44:49 +0000 UTC',
		'Wed, 22 Dec 1909 06:20:37 +0000 UTC',
		'Mon, 21 Mar 1910 07:03:01 +0000 UTC',
		'Wed, 22 Jun 1910 03:48:58 +0000 UTC',
		'Fri, 23 Sep 1910 18:30:49 +0000 UTC',
		'Thu, 22 Dec 1910 12:12:12 +0000 UTC',
		'Tue, 21 Mar 1911 12:54:38 +0000 UTC',
		'Thu, 22 Jun 1911 09:35:32 +0000 UTC',
		'Sun, 24 Sep 1911 00:17:39 +0000 UTC',
		'Fri, 22 Dec 1911 17:53:50 +0000 UTC',
		'Wed, 20 Mar 1912 18:29:14 +0000 UTC',
		'Fri, 21 Jun 1912 15:16:58 +0000 UTC',
		'Mon, 23 Sep 1912 06:08:18 +0000 UTC',
		'Sat, 21 Dec 1912 23:45:00 +0000 UTC',
		'Fri, 21 Mar 1913 01:18:34 +0000 UTC',
		'Sat, 21 Jun 1913 21:09:49 +0000 UTC',
		'Tue, 23 Sep 1913 11:52:36 +0000 UTC',
		'Mon, 22 Dec 1913 05:35:07 +0000 UTC',
		'Sat, 21 Mar 1914 06:10:57 +0000 UTC',
		'Mon, 22 Jun 1914 02:55:11 +0000 UTC',
		'Wed, 23 Sep 1914 17:34:14 +0000 UTC',
		'Tue, 22 Dec 1914 11:22:31 +0000 UTC',
		'Sun, 21 Mar 1915 11:51:34 +0000 UTC',
		'Tue, 22 Jun 1915 08:29:39 +0000 UTC',
		'Thu, 23 Sep 1915 23:24:03 +0000 UTC',
		'Wed, 22 Dec 1915 17:15:47 +0000 UTC',
		'Mon, 20 Mar 1916 17:47:05 +0000 UTC',
		'Wed, 21 Jun 1916 14:24:35 +0000 UTC',
		'Sat, 23 Sep 1916 05:15:09 +0000 UTC',
		'Thu, 21 Dec 1916 22:58:33 +0000 UTC',
		'Tue, 20 Mar 1917 23:37:34 +0000 UTC',
		'Thu, 21 Jun 1917 20:14:19 +0000 UTC',
		'Sun, 23 Sep 1917 11:00:11 +0000 UTC',
		'Sat, 22 Dec 1917 04:45:56 +0000 UTC',
		'Thu, 21 Mar 1918 06:25:53 +0000 UTC',
		'Sat, 22 Jun 1918 01:59:41 +0000 UTC',
		'Mon, 23 Sep 1918 16:45:42 +0000 UTC',
		'Sun, 22 Dec 1918 10:41:29 +0000 UTC',
		'Fri, 21 Mar 1919 12:19:27 +0000 UTC',
		'Sun, 22 Jun 1919 07:53:45 +0000 UTC',
		'Tue, 23 Sep 1919 22:35:38 +0000 UTC',
		'Mon, 22 Dec 1919 16:27:17 +0000 UTC',
		'Sat, 20 Mar 1920 16:59:10 +0000 UTC',
		'Mon, 21 Jun 1920 13:40:20 +0000 UTC',
		'Thu, 23 Sep 1920 04:28:33 +0000 UTC',
		'Tue, 21 Dec 1920 22:17:19 +0000 UTC',
		'Sun, 20 Mar 1921 22:51:45 +0000 UTC',
		'Tue, 21 Jun 1921 19:36:03 +0000 UTC',
		'Fri, 23 Sep 1921 10:19:44 +0000 UTC',
		'Thu, 22 Dec 1921 04:07:54 +0000 UTC',
		'Tue, 21 Mar 1922 04:48:27 +0000 UTC',
		'Thu, 22 Jun 1922 01:26:48 +0000 UTC',
		'Sat, 23 Sep 1922 16:09:46 +0000 UTC',
		'Fri, 22 Dec 1922 09:57:03 +0000 UTC',
		'Wed, 21 Mar 1923 10:28:56 +0000 UTC',
		'Fri, 22 Jun 1923 07:03:05 +0000 UTC',
		'Sun, 23 Sep 1923 22:03:33 +0000 UTC',
		'Sat, 22 Dec 1923 15:53:35 +0000 UTC',
		'Thu, 20 Mar 1924 16:20:35 +0000 UTC',
		'Sat, 21 Jun 1924 12:59:35 +0000 UTC',
		'Tue, 23 Sep 1924 03:58:41 +0000 UTC',
		'Sun, 21 Dec 1924 21:45:22 +0000 UTC',
		'Fri, 20 Mar 1925 22:12:10 +0000 UTC',
		'Sun, 21 Jun 1925 18:50:02 +0000 UTC',
		'Wed, 23 Sep 1925 09:43:11 +0000 UTC',
		'Tue, 22 Dec 1925 03:37:03 +0000 UTC',
		'Sun, 21 Mar 1926 04:01:05 +0000 UTC',
		'Tue, 22 Jun 1926 00:29:55 +0000 UTC',
		'Thu, 23 Sep 1926 15:26:43 +0000 UTC',
		'Wed, 22 Dec 1926 09:33:40 +0000 UTC',
		'Mon, 21 Mar 1927 09:59:28 +0000 UTC',
		'Wed, 22 Jun 1927 06:22:10 +0000 UTC',
		'Fri, 23 Sep 1927 21:17:18 +0000 UTC',
		'Thu, 22 Dec 1927 15:18:35 +0000 UTC',
		'Tue, 20 Mar 1928 15:44:03 +0000 UTC',
		'Thu, 21 Jun 1928 12:06:22 +0000 UTC',
		'Sun, 23 Sep 1928 03:05:27 +0000 UTC',
		'Fri, 21 Dec 1928 21:03:51 +0000 UTC',
		'Wed, 20 Mar 1929 22:35:32 +0000 UTC',
		'Fri, 21 Jun 1929 18:00:43 +0000 UTC',
		'Mon, 23 Sep 1929 08:52:20 +0000 UTC',
		'Sun, 22 Dec 1929 02:53:07 +0000 UTC',
		'Fri, 21 Mar 1930 04:29:54 +0000 UTC',
		'Sat, 21 Jun 1930 23:53:05 +0000 UTC',
		'Tue, 23 Sep 1930 14:36:30 +0000 UTC',
		'Mon, 22 Dec 1930 08:39:27 +0000 UTC',
		'Sat, 21 Mar 1931 09:06:24 +0000 UTC',
		'Mon, 22 Jun 1931 05:28:31 +0000 UTC',
		'Wed, 23 Sep 1931 20:23:29 +0000 UTC',
		'Tue, 22 Dec 1931 14:29:41 +0000 UTC',
		'Sun, 20 Mar 1932 14:53:56 +0000 UTC',
		'Tue, 21 Jun 1932 11:22:59 +0000 UTC',
		'Fri, 23 Sep 1932 02:16:07 +0000 UTC',
		'Wed, 21 Dec 1932 20:14:14 +0000 UTC',
		'Mon, 20 Mar 1933 20:43:29 +0000 UTC',
		'Wed, 21 Jun 1933 17:11:48 +0000 UTC',
		'Sat, 23 Sep 1933 08:00:35 +0000 UTC',
		'Fri, 22 Dec 1933 01:57:52 +0000 UTC',
		'Wed, 21 Mar 1934 02:27:32 +0000 UTC',
		'Thu, 21 Jun 1934 22:47:36 +0000 UTC',
		'Sun, 23 Sep 1934 13:44:52 +0000 UTC',
		'Sat, 22 Dec 1934 07:49:19 +0000 UTC',
		'Thu, 21 Mar 1935 09:17:57 +0000 UTC',
		'Sat, 22 Jun 1935 04:38:03 +0000 UTC',
		'Mon, 23 Sep 1935 19:38:18 +0000 UTC',
		'Sun, 22 Dec 1935 13:37:00 +0000 UTC',
		'Fri, 20 Mar 1936 13:57:34 +0000 UTC',
		'Sun, 21 Jun 1936 10:21:34 +0000 UTC',
		'Wed, 23 Sep 1936 01:26:06 +0000 UTC',
		'Mon, 21 Dec 1936 19:26:47 +0000 UTC',
		'Sat, 20 Mar 1937 19:45:25 +0000 UTC',
		'Mon, 21 Jun 1937 16:12:07 +0000 UTC',
		'Thu, 23 Sep 1937 07:12:52 +0000 UTC',
		'Wed, 22 Dec 1937 01:22:07 +0000 UTC',
		'Mon, 21 Mar 1938 01:43:14 +0000 UTC',
		'Tue, 21 Jun 1938 22:03:42 +0000 UTC',
		'Fri, 23 Sep 1938 13:00:08 +0000 UTC',
		'Thu, 22 Dec 1938 07:13:30 +0000 UTC',
		'Tue, 21 Mar 1939 07:28:39 +0000 UTC',
		'Thu, 22 Jun 1939 03:39:27 +0000 UTC',
		'Sat, 23 Sep 1939 18:49:32 +0000 UTC',
		'Fri, 22 Dec 1939 13:05:46 +0000 UTC',
		'Wed, 20 Mar 1940 13:23:53 +0000 UTC',
		'Fri, 21 Jun 1940 09:36:34 +0000 UTC',
		'Mon, 23 Sep 1940 00:45:39 +0000 UTC',
		'Sat, 21 Dec 1940 18:54:54 +0000 UTC',
		'Thu, 20 Mar 1941 20:20:52 +0000 UTC',
		'Sat, 21 Jun 1941 15:33:18 +0000 UTC',
		'Tue, 23 Sep 1941 06:32:49 +0000 UTC',
		'Mon, 22 Dec 1941 00:44:53 +0000 UTC',
		'Sat, 21 Mar 1942 01:10:15 +0000 UTC',
		'Sun, 21 Jun 1942 21:16:03 +0000 UTC',
		'Wed, 23 Sep 1942 12:16:33 +0000 UTC',
		'Tue, 22 Dec 1942 06:39:26 +0000 UTC',
		'Sun, 21 Mar 1943 07:02:47 +0000 UTC',
		'Tue, 22 Jun 1943 03:12:17 +0000 UTC',
		'Thu, 23 Sep 1943 18:11:30 +0000 UTC',
		'Wed, 22 Dec 1943 12:29:12 +0000 UTC',
		'Mon, 20 Mar 1944 12:48:30 +0000 UTC',
		'Wed, 21 Jun 1944 09:02:15 +0000 UTC',
		'Sat, 23 Sep 1944 00:01:34 +0000 UTC',
		'Thu, 21 Dec 1944 18:15:07 +0000 UTC',
		'Tue, 20 Mar 1945 18:37:42 +0000 UTC',
		'Thu, 21 Jun 1945 14:52:09 +0000 UTC',
		'Sun, 23 Sep 1945 05:49:21 +0000 UTC',
		'Sat, 22 Dec 1945 00:03:51 +0000 UTC',
		'Thu, 21 Mar 1946 01:32:42 +0000 UTC',
		'Fri, 21 Jun 1946 20:44:46 +0000 UTC',
		'Mon, 23 Sep 1946 11:41:11 +0000 UTC',
		'Sun, 22 Dec 1946 05:53:22 +0000 UTC',
		'Fri, 21 Mar 1947 07:13:02 +0000 UTC',
		'Sun, 22 Jun 1947 02:19:22 +0000 UTC',
		'Tue, 23 Sep 1947 17:28:55 +0000 UTC',
		'Mon, 22 Dec 1947 11:42:56 +0000 UTC',
		'Sat, 20 Mar 1948 11:57:17 +0000 UTC',
		'Mon, 21 Jun 1948 08:10:44 +0000 UTC',
		'Wed, 22 Sep 1948 23:21:49 +0000 UTC',
		'Tue, 21 Dec 1948 17:33:35 +0000 UTC',
		'Sun, 20 Mar 1949 17:48:28 +0000 UTC',
		'Tue, 21 Jun 1949 14:02:48 +0000 UTC',
		'Fri, 23 Sep 1949 05:05:50 +0000 UTC',
		'Wed, 21 Dec 1949 23:23:29 +0000 UTC',
		'Mon, 20 Mar 1950 23:35:07 +0000 UTC',
		'Wed, 21 Jun 1950 19:36:13 +0000 UTC',
		'Sat, 23 Sep 1950 10:43:58 +0000 UTC',
		'Fri, 22 Dec 1950 05:13:11 +0000 UTC',
		'Wed, 21 Mar 1951 05:26:12 +0000 UTC',
		'Fri, 22 Jun 1951 01:24:57 +0000 UTC',
		'Sun, 23 Sep 1951 16:37:01 +0000 UTC',
		'Sat, 22 Dec 1951 11:00:06 +0000 UTC',
		'Thu, 20 Mar 1952 11:13:34 +0000 UTC',
		'Sat, 21 Jun 1952 07:12:37 +0000 UTC',
		'Mon, 22 Sep 1952 22:23:55 +0000 UTC',
		'Sun, 21 Dec 1952 16:43:41 +0000 UTC',
		'Fri, 20 Mar 1953 17:00:55 +0000 UTC',
		'Sun, 21 Jun 1953 12:59:49 +0000 UTC',
		'Wed, 23 Sep 1953 04:05:55 +0000 UTC',
		'Mon, 21 Dec 1953 22:31:51 +0000 UTC',
		'Sat, 20 Mar 1954 22:53:17 +0000 UTC',
		'Mon, 21 Jun 1954 18:54:19 +0000 UTC',
		'Thu, 23 Sep 1954 09:55:49 +0000 UTC',
		'Wed, 22 Dec 1954 04:24:10 +0000 UTC',
		'Mon, 21 Mar 1955 04:35:28 +0000 UTC',
		'Wed, 22 Jun 1955 00:32:02 +0000 UTC',
		'Fri, 23 Sep 1955 15:40:57 +0000 UTC',
		'Thu, 22 Dec 1955 10:11:29 +0000 UTC',
		'Tue, 20 Mar 1956 10:21:09 +0000 UTC',
		'Thu, 21 Jun 1956 06:24:05 +0000 UTC',
		'Sat, 22 Sep 1956 21:35:24 +0000 UTC',
		'Fri, 21 Dec 1956 16:00:06 +0000 UTC',
		'Wed, 20 Mar 1957 17:16:49 +0000 UTC',
		'Fri, 21 Jun 1957 12:20:46 +0000 UTC',
		'Mon, 23 Sep 1957 03:26:05 +0000 UTC',
		'Sat, 21 Dec 1957 21:49:18 +0000 UTC',
		'Thu, 20 Mar 1958 23:05:38 +0000 UTC',
		'Sat, 21 Jun 1958 17:57:02 +0000 UTC',
		'Tue, 23 Sep 1958 09:09:01 +0000 UTC',
		'Mon, 22 Dec 1958 03:40:03 +0000 UTC',
		'Sat, 21 Mar 1959 03:55:07 +0000 UTC',
		'Sun, 21 Jun 1959 23:49:50 +0000 UTC',
		'Wed, 23 Sep 1959 15:08:28 +0000 UTC',
		'Tue, 22 Dec 1959 09:34:33 +0000 UTC',
		'Sun, 20 Mar 1960 09:42:37 +0000 UTC',
		'Tue, 21 Jun 1960 05:42:09 +0000 UTC',
		'Thu, 22 Sep 1960 20:58:48 +0000 UTC',
		'Wed, 21 Dec 1960 15:26:12 +0000 UTC',
		'Mon, 20 Mar 1961 15:32:33 +0000 UTC',
		'Wed, 21 Jun 1961 11:30:12 +0000 UTC',
		'Sat, 23 Sep 1961 02:42:31 +0000 UTC',
		'Thu, 21 Dec 1961 21:19:40 +0000 UTC',
		'Tue, 20 Mar 1962 21:29:48 +0000 UTC',
		'Thu, 21 Jun 1962 17:24:33 +0000 UTC',
		'Sun, 23 Sep 1962 08:35:58 +0000 UTC',
		'Sat, 22 Dec 1962 03:14:52 +0000 UTC',
		'Thu, 21 Mar 1963 04:19:57 +0000 UTC',
		'Fri, 21 Jun 1963 23:04:06 +0000 UTC',
		'Mon, 23 Sep 1963 14:23:32 +0000 UTC',
		'Sun, 22 Dec 1963 09:02:08 +0000 UTC',
		'Fri, 20 Mar 1964 09:10:01 +0000 UTC',
		'Sun, 21 Jun 1964 04:56:31 +0000 UTC',
		'Tue, 22 Sep 1964 20:16:51 +0000 UTC',
		'Mon, 21 Dec 1964 14:49:51 +0000 UTC',
		'Sat, 20 Mar 1965 15:05:05 +0000 UTC',
		'Mon, 21 Jun 1965 10:55:45 +0000 UTC',
		'Thu, 23 Sep 1965 02:06:04 +0000 UTC',
		'Tue, 21 Dec 1965 20:40:39 +0000 UTC',
		'Sun, 20 Mar 1966 20:52:42 +0000 UTC',
		'Tue, 21 Jun 1966 16:33:33 +0000 UTC',
		'Fri, 23 Sep 1966 07:43:28 +0000 UTC',
		'Thu, 22 Dec 1966 02:28:08 +0000 UTC',
		'Tue, 21 Mar 1967 02:37:12 +0000 UTC',
		'Wed, 21 Jun 1967 22:23:17 +0000 UTC',
		'Sat, 23 Sep 1967 13:38:02 +0000 UTC',
		'Fri, 22 Dec 1967 08:16:44 +0000 UTC',
		'Wed, 20 Mar 1968 08:21:57 +0000 UTC',
		'Fri, 21 Jun 1968 04:13:18 +0000 UTC',
		'Sun, 22 Sep 1968 19:26:22 +0000 UTC',
		'Sat, 21 Dec 1968 14:00:17 +0000 UTC',
		'Thu, 20 Mar 1969 15:08:09 +0000 UTC',
		'Sat, 21 Jun 1969 09:55:09 +0000 UTC',
		'Tue, 23 Sep 1969 01:06:46 +0000 UTC',
		'Sun, 21 Dec 1969 19:44:01 +0000 UTC',
		'Fri, 20 Mar 1970 19:56:25 +0000 UTC',
		'Sun, 21 Jun 1970 15:42:58 +0000 UTC',
		'Wed, 23 Sep 1970 06:59:09 +0000 UTC',
		'Tue, 22 Dec 1970 01:35:43 +0000 UTC',
		'Sun, 21 Mar 1971 01:38:21 +0000 UTC',
		'Mon, 21 Jun 1971 21:19:20 +0000 UTC',
		'Thu, 23 Sep 1971 12:44:36 +0000 UTC',
		'Wed, 22 Dec 1971 07:23:53 +0000 UTC',
		'Mon, 20 Mar 1972 07:21:11 +0000 UTC',
		'Wed, 21 Jun 1972 03:05:51 +0000 UTC',
		'Fri, 22 Sep 1972 18:32:37 +0000 UTC',
		'Thu, 21 Dec 1972 13:13:02 +0000 UTC',
		'Tue, 20 Mar 1973 13:12:45 +0000 UTC',
		'Thu, 21 Jun 1973 09:00:42 +0000 UTC',
		'Sun, 23 Sep 1973 00:21:06 +0000 UTC',
		'Fri, 21 Dec 1973 19:08:10 +0000 UTC',
		'Wed, 20 Mar 1974 20:06:36 +0000 UTC',
		'Fri, 21 Jun 1974 14:37:32 +0000 UTC',
		'Mon, 23 Sep 1974 05:58:50 +0000 UTC',
		'Sun, 22 Dec 1974 00:55:46 +0000 UTC',
		'Fri, 21 Mar 1975 01:57:01 +0000 UTC',
		'Sat, 21 Jun 1975 20:26:10 +0000 UTC',
		'Tue, 23 Sep 1975 11:54:52 +0000 UTC',
		'Mon, 22 Dec 1975 06:45:44 +0000 UTC',
		'Sat, 20 Mar 1976 06:49:40 +0000 UTC',
		'Mon, 21 Jun 1976 02:24:05 +0000 UTC',
		'Wed, 22 Sep 1976 17:48:35 +0000 UTC',
		'Tue, 21 Dec 1976 12:35:20 +0000 UTC',
		'Sun, 20 Mar 1977 12:42:42 +0000 UTC',
		'Tue, 21 Jun 1977 08:13:57 +0000 UTC',
		'Thu, 22 Sep 1977 23:29:25 +0000 UTC',
		'Wed, 21 Dec 1977 18:23:00 +0000 UTC',
		'Mon, 20 Mar 1978 18:33:38 +0000 UTC',
		'Wed, 21 Jun 1978 14:10:03 +0000 UTC',
		'Sat, 23 Sep 1978 05:25:51 +0000 UTC',
		'Fri, 22 Dec 1978 00:20:50 +0000 UTC',
		'Wed, 21 Mar 1979 00:22:03 +0000 UTC',
		'Thu, 21 Jun 1979 19:56:22 +0000 UTC',
		'Sun, 23 Sep 1979 11:16:21 +0000 UTC',
		'Sat, 22 Dec 1979 06:10:02 +0000 UTC',
		'Thu, 20 Mar 1980 06:09:41 +0000 UTC',
		'Sat, 21 Jun 1980 01:46:49 +0000 UTC',
		'Mon, 22 Sep 1980 17:08:32 +0000 UTC',
		'Sun, 21 Dec 1980 11:56:14 +0000 UTC',
		'Fri, 20 Mar 1981 12:02:55 +0000 UTC',
		'Sun, 21 Jun 1981 07:44:45 +0000 UTC',
		'Tue, 22 Sep 1981 23:04:45 +0000 UTC',
		'Mon, 21 Dec 1981 17:50:47 +0000 UTC',
		'Sat, 20 Mar 1982 17:55:32 +0000 UTC',
		'Mon, 21 Jun 1982 13:23:08 +0000 UTC',
		'Thu, 23 Sep 1982 04:46:14 +0000 UTC',
		'Tue, 21 Dec 1982 23:38:13 +0000 UTC',
		'Sun, 20 Mar 1983 23:38:47 +0000 UTC',
		'Tue, 21 Jun 1983 19:08:28 +0000 UTC',
		'Fri, 23 Sep 1983 10:41:27 +0000 UTC',
		'Thu, 22 Dec 1983 05:30:14 +0000 UTC',
		'Tue, 20 Mar 1984 05:24:08 +0000 UTC',
		'Thu, 21 Jun 1984 01:02:30 +0000 UTC',
		'Sat, 22 Sep 1984 16:33:19 +0000 UTC',
		'Fri, 21 Dec 1984 11:23:16 +0000 UTC',
		'Wed, 20 Mar 1985 11:14:18 +0000 UTC',
		'Fri, 21 Jun 1985 06:44:21 +0000 UTC',
		'Sun, 22 Sep 1985 22:07:34 +0000 UTC',
		'Sat, 21 Dec 1985 17:08:01 +0000 UTC',
		'Thu, 20 Mar 1986 17:02:55 +0000 UTC',
		'Sat, 21 Jun 1986 12:30:03 +0000 UTC',
		'Tue, 23 Sep 1986 03:59:07 +0000 UTC',
		'Sun, 21 Dec 1986 23:01:50 +0000 UTC',
		'Fri, 20 Mar 1987 22:52:16 +0000 UTC',
		'Sun, 21 Jun 1987 18:10:50 +0000 UTC',
		'Wed, 23 Sep 1987 09:45:11 +0000 UTC',
		'Tue, 22 Dec 1987 04:46:18 +0000 UTC',
		'Sun, 20 Mar 1988 04:39:04 +0000 UTC',
		'Mon, 20 Jun 1988 23:56:25 +0000 UTC',
		'Thu, 22 Sep 1988 15:29:17 +0000 UTC',
		'Wed, 21 Dec 1988 10:28:14 +0000 UTC',
		'Mon, 20 Mar 1989 10:28:33 +0000 UTC',
		'Wed, 21 Jun 1989 05:53:01 +0000 UTC',
		'Fri, 22 Sep 1989 21:19:35 +0000 UTC',
		'Thu, 21 Dec 1989 16:22:01 +0000 UTC',
		'Tue, 20 Mar 1990 16:19:02 +0000 UTC',
		'Thu, 21 Jun 1990 11:32:40 +0000 UTC',
		'Sun, 23 Sep 1990 02:55:32 +0000 UTC',
		'Fri, 21 Dec 1990 22:07:05 +0000 UTC',
		'Wed, 20 Mar 1991 22:02:11 +0000 UTC',
		'Fri, 21 Jun 1991 17:18:38 +0000 UTC',
		'Mon, 23 Sep 1991 08:47:58 +0000 UTC',
		'Sun, 22 Dec 1991 03:53:55 +0000 UTC',
		'Fri, 20 Mar 1992 03:48:09 +0000 UTC',
		'Sat, 20 Jun 1992 23:14:19 +0000 UTC',
		'Tue, 22 Sep 1992 14:42:51 +0000 UTC',
		'Mon, 21 Dec 1992 09:43:15 +0000 UTC',
		'Sat, 20 Mar 1993 09:41:08 +0000 UTC',
		'Mon, 21 Jun 1993 05:00:07 +0000 UTC',
		'Wed, 22 Sep 1993 20:22:34 +0000 UTC',
		'Tue, 21 Dec 1993 15:26:09 +0000 UTC',
		'Sun, 20 Mar 1994 15:28:09 +0000 UTC',
		'Tue, 21 Jun 1994 10:48:03 +0000 UTC',
		'Fri, 23 Sep 1994 02:19:36 +0000 UTC',
		'Wed, 21 Dec 1994 21:22:50 +0000 UTC',
		'Mon, 20 Mar 1995 21:14:44 +0000 UTC',
		'Wed, 21 Jun 1995 16:34:27 +0000 UTC',
		'Sat, 23 Sep 1995 08:13:09 +0000 UTC',
		'Fri, 22 Dec 1995 03:17:11 +0000 UTC',
		'Wed, 20 Mar 1996 03:03:12 +0000 UTC',
		'Thu, 20 Jun 1996 22:23:36 +0000 UTC',
		'Sun, 22 Sep 1996 14:00:21 +0000 UTC',
		'Sat, 21 Dec 1996 09:06:16 +0000 UTC',
		'Thu, 20 Mar 1997 08:55:06 +0000 UTC',
		'Sat, 21 Jun 1997 04:20:06 +0000 UTC',
		'Mon, 22 Sep 1997 19:55:33 +0000 UTC',
		'Sun, 21 Dec 1997 15:07:16 +0000 UTC',
		'Fri, 20 Mar 1998 14:54:27 +0000 UTC',
		'Sun, 21 Jun 1998 10:02:27 +0000 UTC',
		'Wed, 23 Sep 1998 01:37:33 +0000 UTC',
		'Mon, 21 Dec 1998 20:56:30 +0000 UTC',
		'Sat, 20 Mar 1999 20:45:55 +0000 UTC',
		'Mon, 21 Jun 1999 15:49:09 +0000 UTC',
		'Thu, 23 Sep 1999 07:31:34 +0000 UTC',
		'Wed, 22 Dec 1999 02:44:14 +0000 UTC',
		'Mon, 20 Mar 2000 02:35:24 +0000 UTC',
		'Tue, 20 Jun 2000 21:47:43 +0000 UTC',
		'Fri, 22 Sep 2000 13:27:50 +0000 UTC',
		'Thu, 21 Dec 2000 08:37:40 +0000 UTC',
		'Tue, 20 Mar 2001 08:30:59 +0000 UTC',
		'Thu, 21 Jun 2001 03:37:44 +0000 UTC',
		'Sat, 22 Sep 2001 19:04:30 +0000 UTC',
		'Fri, 21 Dec 2001 14:21:41 +0000 UTC',
		'Wed, 20 Mar 2002 14:16:15 +0000 UTC',
		'Fri, 21 Jun 2002 09:24:52 +0000 UTC',
		'Mon, 23 Sep 2002 00:55:33 +0000 UTC',
		'Sat, 21 Dec 2002 20:14:45 +0000 UTC',
		'Thu, 20 Mar 2003 20:00:20 +0000 UTC',
		'Sat, 21 Jun 2003 15:10:36 +0000 UTC',
		'Tue, 23 Sep 2003 06:46:55 +0000 UTC',
		'Mon, 22 Dec 2003 02:03:53 +0000 UTC',
		'Sat, 20 Mar 2004 01:48:40 +0000 UTC',
		'Sun, 20 Jun 2004 20:56:43 +0000 UTC',
		'Wed, 22 Sep 2004 12:29:53 +0000 UTC',
		'Tue, 21 Dec 2004 07:41:39 +0000 UTC',
		'Sun, 20 Mar 2005 07:33:35 +0000 UTC',
		'Tue, 21 Jun 2005 02:46:03 +0000 UTC',
		'Thu, 22 Sep 2005 18:22:35 +0000 UTC',
		'Wed, 21 Dec 2005 13:35:16 +0000 UTC',
		'Mon, 20 Mar 2006 13:25:16 +0000 UTC',
		'Wed, 21 Jun 2006 08:25:31 +0000 UTC',
		'Sat, 23 Sep 2006 00:03:30 +0000 UTC',
		'Thu, 21 Dec 2006 19:21:51 +0000 UTC',
		'Tue, 20 Mar 2007 20:07:13 +0000 UTC',
		'Thu, 21 Jun 2007 14:05:57 +0000 UTC',
		'Sun, 23 Sep 2007 05:50:50 +0000 UTC',
		'Sat, 22 Dec 2007 01:07:36 +0000 UTC',
		'Thu, 20 Mar 2008 01:48:14 +0000 UTC',
		'Fri, 20 Jun 2008 19:59:15 +0000 UTC',
		'Mon, 22 Sep 2008 11:44:26 +0000 UTC',
		'Sun, 21 Dec 2008 07:03:35 +0000 UTC',
		'Fri, 20 Mar 2009 07:43:47 +0000 UTC',
		'Sun, 21 Jun 2009 01:45:23 +0000 UTC',
		'Tue, 22 Sep 2009 17:18:30 +0000 UTC',
		'Mon, 21 Dec 2009 12:46:32 +0000 UTC',
		'Sat, 20 Mar 2010 13:31:45 +0000 UTC',
		'Mon, 21 Jun 2010 07:28:16 +0000 UTC',
		'Wed, 22 Sep 2010 23:09:10 +0000 UTC',
		'Tue, 21 Dec 2010 18:38:13 +0000 UTC',
		'Sun, 20 Mar 2011 19:20:33 +0000 UTC',
		'Tue, 21 Jun 2011 13:16:10 +0000 UTC',
		'Fri, 23 Sep 2011 05:04:25 +0000 UTC',
		'Thu, 22 Dec 2011 00:29:59 +0000 UTC',
		'Tue, 20 Mar 2012 01:14:13 +0000 UTC',
		'Wed, 20 Jun 2012 19:08:09 +0000 UTC',
		'Sat, 22 Sep 2012 10:48:48 +0000 UTC',
		'Fri, 21 Dec 2012 06:11:32 +0000 UTC',
		'Wed, 20 Mar 2013 07:01:36 +0000 UTC',
		'Fri, 21 Jun 2013 01:03:36 +0000 UTC',
		'Sun, 22 Sep 2013 16:43:27 +0000 UTC',
		'Sat, 21 Dec 2013 12:10:56 +0000 UTC',
		'Thu, 20 Mar 2014 12:56:36 +0000 UTC',
		'Sat, 21 Jun 2014 06:51:17 +0000 UTC',
		'Mon, 22 Sep 2014 22:29:09 +0000 UTC',
		'Sun, 21 Dec 2014 18:02:49 +0000 UTC',
		'Fri, 20 Mar 2015 18:44:53 +0000 UTC',
		'Sun, 21 Jun 2015 12:37:33 +0000 UTC',
		'Wed, 23 Sep 2015 04:20:06 +0000 UTC',
		'Mon, 21 Dec 2015 23:47:53 +0000 UTC',
		'Sun, 20 Mar 2016 00:29:48 +0000 UTC',
		'Mon, 20 Jun 2016 18:33:55 +0000 UTC',
		'Thu, 22 Sep 2016 10:20:41 +0000 UTC',
		'Wed, 21 Dec 2016 05:44:00 +0000 UTC',
		'Mon, 20 Mar 2017 06:28:31 +0000 UTC',
		'Wed, 21 Jun 2017 00:23:42 +0000 UTC',
		'Fri, 22 Sep 2017 16:01:07 +0000 UTC',
		'Thu, 21 Dec 2017 11:27:50 +0000 UTC',
		'Tue, 20 Mar 2018 12:14:45 +0000 UTC',
		'Thu, 21 Jun 2018 06:06:42 +0000 UTC',
		'Sat, 22 Sep 2018 21:53:41 +0000 UTC',
		'Fri, 21 Dec 2018 17:21:51 +0000 UTC',
		'Wed, 20 Mar 2019 17:58:05 +0000 UTC',
		'Fri, 21 Jun 2019 11:53:44 +0000 UTC',
		'Mon, 23 Sep 2019 03:49:29 +0000 UTC',
		'Sat, 21 Dec 2019 23:18:56 +0000 UTC',
		'Thu, 19 Mar 2020 23:49:35 +0000 UTC',
		'Sat, 20 Jun 2020 17:43:02 +0000 UTC',
		'Tue, 22 Sep 2020 09:30:27 +0000 UTC',
		'Mon, 21 Dec 2020 05:02:26 +0000 UTC',
		'Sat, 20 Mar 2021 05:37:05 +0000 UTC',
		'Sun, 20 Jun 2021 23:31:32 +0000 UTC',
		'Wed, 22 Sep 2021 15:20:30 +0000 UTC',
		'Tue, 21 Dec 2021 10:58:54 +0000 UTC',
		'Sun, 20 Mar 2022 11:32:53 +0000 UTC',
		'Tue, 21 Jun 2022 05:13:18 +0000 UTC',
		'Thu, 22 Sep 2022 21:03:41 +0000 UTC',
		'Wed, 21 Dec 2022 16:47:33 +0000 UTC',
		'Mon, 20 Mar 2023 17:24:14 +0000 UTC',
		'Wed, 21 Jun 2023 10:57:11 +0000 UTC',
		'Sat, 23 Sep 2023 02:49:36 +0000 UTC',
		'Thu, 21 Dec 2023 22:27:06 +0000 UTC',
		'Tue, 19 Mar 2024 23:06:04 +0000 UTC',
		'Thu, 20 Jun 2024 16:50:23 +0000 UTC',
		'Sun, 22 Sep 2024 08:43:12 +0000 UTC',
		'Sat, 21 Dec 2024 04:19:54 +0000 UTC',
		'Thu, 20 Mar 2025 05:01:03 +0000 UTC',
		'Fri, 20 Jun 2025 22:41:49 +0000 UTC',
		'Mon, 22 Sep 2025 14:19:00 +0000 UTC',
		'Sun, 21 Dec 2025 10:02:36 +0000 UTC',
		'Fri, 20 Mar 2026 10:45:02 +0000 UTC',
		'Sun, 21 Jun 2026 04:24:21 +0000 UTC',
		'Tue, 22 Sep 2026 20:04:56 +0000 UTC',
		'Mon, 21 Dec 2026 15:49:39 +0000 UTC',
		'Sat, 20 Mar 2027 16:24:18 +0000 UTC',
		'Mon, 21 Jun 2027 10:10:06 +0000 UTC',
		'Thu, 23 Sep 2027 02:00:43 +0000 UTC',
		'Tue, 21 Dec 2027 21:41:41 +0000 UTC',
		'Sun, 19 Mar 2028 22:16:32 +0000 UTC',
		'Tue, 20 Jun 2028 16:00:57 +0000 UTC',
		'Fri, 22 Sep 2028 07:44:31 +0000 UTC',
		'Thu, 21 Dec 2028 03:19:30 +0000 UTC',
		'Tue, 20 Mar 2029 04:01:03 +0000 UTC',
		'Wed, 20 Jun 2029 21:47:43 +0000 UTC',
		'Sat, 22 Sep 2029 13:37:17 +0000 UTC',
		'Fri, 21 Dec 2029 09:13:45 +0000 UTC',
		'Wed, 20 Mar 2030 09:51:15 +0000 UTC',
		'Fri, 21 Jun 2030 03:30:42 +0000 UTC',
		'Sun, 22 Sep 2030 19:26:34 +0000 UTC',
		'Sat, 21 Dec 2030 15:08:54 +0000 UTC',
		'Thu, 20 Mar 2031 15:40:26 +0000 UTC',
		'Sat, 21 Jun 2031 09:16:33 +0000 UTC',
		'Tue, 23 Sep 2031 01:14:45 +0000 UTC',
		'Sun, 21 Dec 2031 20:55:23 +0000 UTC',
		'Fri, 19 Mar 2032 21:21:30 +0000 UTC',
		'Sun, 20 Jun 2032 15:08:02 +0000 UTC',
		'Wed, 22 Sep 2032 07:10:06 +0000 UTC',
		'Tue, 21 Dec 2032 02:55:29 +0000 UTC',
		'Sun, 20 Mar 2033 03:22:18 +0000 UTC',
		'Mon, 20 Jun 2033 21:00:19 +0000 UTC',
		'Thu, 22 Sep 2033 12:51:07 +0000 UTC',
		'Wed, 21 Dec 2033 08:44:58 +0000 UTC',
		'Mon, 20 Mar 2034 09:16:53 +0000 UTC',
		'Wed, 21 Jun 2034 02:43:46 +0000 UTC',
		'Fri, 22 Sep 2034 18:39:03 +0000 UTC',
		'Thu, 21 Dec 2034 14:33:16 +0000 UTC',
		'Tue, 20 Mar 2035 15:02:42 +0000 UTC',
		'Thu, 21 Jun 2035 08:32:07 +0000 UTC',
		'Sun, 23 Sep 2035 00:38:06 +0000 UTC',
		'Fri, 21 Dec 2035 20:30:12 +0000 UTC',
		'Wed, 19 Mar 2036 21:01:59 +0000 UTC',
		'Fri, 20 Jun 2036 14:30:51 +0000 UTC',
		'Mon, 22 Sep 2036 06:22:48 +0000 UTC',
		'Sun, 21 Dec 2036 02:12:04 +0000 UTC',
		'Fri, 20 Mar 2037 02:49:01 +0000 UTC',
		'Sat, 20 Jun 2037 20:21:34 +0000 UTC',
		'Tue, 22 Sep 2037 12:12:00 +0000 UTC',
		'Mon, 21 Dec 2037 08:06:59 +0000 UTC'
	);
	
	
	/**
	 * Determines current season by a date
	 * 
	 * @param  mixed $date  The date or timestamp (in string or fTimestamp object format), to determine a season for
	 * @return string  Which season the date is, in 'Season Year(-Year)' format
	 */
	private static function determine($date, $formatted=FALSE)
	{
		$date		    = new fTimestamp($date);
		$season_changes = self::$season_changes;
		
		foreach ($season_changes as $i => $season_change) {
			// need the first two to compare
			if ($i == 0) {
				continue;
			}
			
			if ($date->gte($season_changes[$i - 1]) && $date->lte($season_changes[$i])) {
				$current_season = $season_changes[$i - 1];
			}
		}
		
		if ($formatted) {
			return self::returnFormattedSeason($current_season);
		}
		return $current_season;
	}
	
	
	/**
	 * Converts a timestamp into a formatted string for a season
	 * 
	 * @param  fTimestamp object $season  The timestamp of the season to format
	 * @return string  Formatted string of the season
	 */
	private static function returnFormattedSeason($season)
	{
		$season = new fTimestamp($season);
		
		switch ($season->format('m')) {
			case '03':
				return 'Spring ' . $season->format('Y');
			case '06':
				return 'Summer ' . $season->format('Y');
			case '09':
				return 'Fall ' . $season->format('Y');
			case '12':
				return 'Winter ' . $season->format('Y') . '-' . $season->adjust('+1 year')->format('Y');
		}
		
		return FALSE;
	}
	
	
	/**
	 * Initialize the season class
	 * 
	 * @param  mixed $date  The date or timestamp (in string or fTimestamp object format), to determine a season for
	 * @return Season
	 */
	public function __construct($date)
	{
		// generate fTimestamp objects of the season changes
		if (count(self::$season_changes) == 0) {
			foreach (self::$season_change_strings as $i => $season_change) {
				self::$season_changes[] = new fTimestamp($season_change);
			}
		}
		
		$this->original_index = array_search(self::determine($date), self::$season_changes);
		$this->index          = $this->original_index;
	}
	
	
	/**
	 * Return the current season
	 * 
	 * @param  boolean $formatted  Whether to return the season as an fTimestamp object (FALSE) or in 'Season Year(-Year)' format (TRUE)
	 * @return fTimestamp|string  If formatted, returns a formatted string, otherwise returns an fTimestamp
	 */
	public function current($formatted=FALSE)
	{
		$date = self::$season_changes[$this->index];
		if ($formatted) {
			return self::returnFormattedSeason($date);
		}
		return new fTimestamp($date);
	}
	
	
	/**
	 * Find a range of seasons from the current season to the date
	 * 
	 * @param  mixed $date  The date or timestamp (in string or fTimestamp object format), to determine a season for
	 * @param  boolean $formatted  Whether to return the season as an fTimestamp object (TRUE) or in 'Season Year(-Year)' format (FALSE)
	 * @return array  An array of either formatted timestamps or fTimestamp objects
	 */
	public function getRange($date, $formatted=FALSE)
	{
		$season_changes_slice = array();
		$current              = $this->current();
		$season               = self::determine($date);
		
		if ($current->lt($season)) {
			$start_season = $current;
			$end_season   = $season;
		} else {
			$start_season = $season;
			$end_season   = $current;
		}
		
		$start_season_key = array_search($start_season, self::$season_changes);
		$end_season_key   = array_search($end_season, self::$season_changes);
		
		$season_changes_slice = array_slice(self::$season_changes, $start_season_key, $end_season_key - $start_season_key + 1);
		$seasons              = array();
		
		if ($formatted) {
			return array_map(__CLASS__ . '::returnFormattedSeason', $season_changes_slice);
		}
		
		return $season_changes_slice;
	}
	
	
	/**
	 * Return how far you have iterated from the original season
	 * 
	 * @internal
	 * 
	 * @return integer  The count from the original season
	 */
	public function key()
	{
		return $this->index - $this->original_index;
	}
	
	
	/**
	 * Iterate to the next season
	 * 
	 * @param  boolean $formatted  Whether to return the season as an fTimestamp object (FALSE) or in 'Season Year(-Year)' format (TRUE)
	 * @return fTimestamp|string|FALSE  If formatted, returns a formatted string, otherwise returns an fTimestamp
	 */
	public function next($formatted=FALSE)
	{
		$this->index++;
		
		if (!isset(self::$season_changes[$this->index])) {
			return FALSE;
		}
		if ($formatted) {
			return self::returnFormattedSeason($this->current());
		}
		return $this->current();
	}
	
	
	/**
	 * Iterate to the previous season
	 * 
	 * @param  boolean $formatted  Whether to return the season as an fTimestamp object (FALSE) or in 'Season Year(-Year)' format (TRUE)
	 * @return fTimestamp|string|FALSE  If formatted, returns a formatted string, otherwise returns an fTimestamp
	 */
	public function previous($formatted=FALSE)
	{
		$this->index--;
		
		if ($this->index < 0) {
			$this->index = 0;
			return FALSE;
		}
		if ($formatted) {
			return self::returnFormattedSeason($this->current());
		}
		return $this->current();
	}
	
	
	/**
	 * Return to the first possible season
	 * 
	 * @internal
	 * 
	 * @return void
	 */
	public function rewind()
	{
		$this->index = 0;
	}
	
	
	/**
	 * Return to the original season
	 * 
	 * @return void
	 */
	public function reset()
	{
		$this->index = $this->original_index;
	}
	
	
	/**
	 * Return whether the current season is a valid season (within the PHP range of time())
	 * 
	 * @return boolean
	 */
	public function valid()
	{
		return isset(self::$season_changes[$this->index]);
	}
}